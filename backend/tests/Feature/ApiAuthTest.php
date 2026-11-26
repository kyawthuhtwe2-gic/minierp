<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! file_exists(storage_path('oauth-private.key'))) {
            $this->artisan('passport:keys');
        }

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test', 'users');
    }

    private function loginAs(User $user, string $password = 'password'): string
    {
        return $this->postJson('/api/login', ['email' => $user->email, 'password' => $password])
            ->assertOk()
            ->json('access_token');
    }

    private function bearer(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_successful_login(): void
    {
        $user = User::factory()->role('admin')->create();

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'token_type', 'access_token', 'expires_at', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonMissingPath('user.password');
    }

    public function test_token_expires_in_60_minutes(): void
    {
        $this->loginAs(User::factory()->create());

        $expires = Token::query()->firstOrFail()->expires_at;
        $this->assertEqualsWithDelta(3600, now()->diffInSeconds($expires, false), 30);
    }

    public function test_incorrect_email(): void
    {
        $this->postJson('/api/login', ['email' => 'nobody@example.com', 'password' => 'password'])
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'message' => 'Invalid email or password.']);
    }

    public function test_incorrect_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'message' => 'Invalid email or password.']);
    }

    public function test_missing_fields(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'x'])->assertStatus(401);
        }

        $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'x'])->assertStatus(429);
    }

    public function test_jwt_authenticates_me(): void
    {
        $user = User::factory()->role('staff')->create();
        $token = $this->loginAs($user);

        $this->getJson('/api/me', $this->bearer($token))
            ->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonMissingPath('user.password');
    }

    public function test_no_token_is_unauthenticated(): void
    {
        $this->getJson('/api/me')
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
        $this->getJson('/api/admin/dashboard')->assertStatus(401);
    }

    public function test_invalid_token_is_unauthenticated(): void
    {
        $this->getJson('/api/me', $this->bearer('not.a.jwt'))
            ->assertStatus(401)
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }

    public function test_expired_token_is_rejected(): void
    {
        // JWT validation uses the real clock, so issue a token that is already expired.
        Passport::personalAccessTokensExpireIn(now()->subMinute());
        $token = $this->loginAs(User::factory()->create());

        $this->getJson('/api/me', $this->bearer($token))->assertStatus(401);
    }

    public function test_admin_accesses_admin_endpoints(): void
    {
        $token = $this->loginAs(User::factory()->role('admin')->create());

        $this->getJson('/api/admin/dashboard', $this->bearer($token))->assertOk();
        $this->getJson('/api/management', $this->bearer($token))->assertOk();
    }

    public function test_staff_cannot_access_admin_endpoints(): void
    {
        $token = $this->loginAs(User::factory()->role('staff')->create());

        $this->getJson('/api/admin/dashboard', $this->bearer($token))
            ->assertStatus(403)
            ->assertExactJson(['success' => false, 'message' => 'You do not have permission.']);
        $this->getJson('/api/staff/dashboard', $this->bearer($token))->assertOk();
    }

    public function test_manager_accesses_management_but_not_admin(): void
    {
        $token = $this->loginAs(User::factory()->role('manager')->create());

        $this->getJson('/api/management', $this->bearer($token))->assertOk();
        $this->getJson('/api/admin/dashboard', $this->bearer($token))->assertStatus(403);
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $token = $this->loginAs(User::factory()->create(['role' => null]));

        $this->getJson('/api/staff/dashboard', $this->bearer($token))->assertStatus(403);
    }

    public function test_role_change_applies_immediately(): void
    {
        $user = User::factory()->role('staff')->create();
        $token = $this->loginAs($user);

        $this->getJson('/api/admin/dashboard', $this->bearer($token))->assertStatus(403);

        $user->forceFill(['role' => 'admin'])->save();
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/admin/dashboard', $this->bearer($token))->assertOk();

        $user->forceFill(['role' => 'staff'])->save();
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/admin/dashboard', $this->bearer($token))->assertStatus(403);
    }

    public function test_logout_revokes_token(): void
    {
        $token = $this->loginAs(User::factory()->create());

        $this->postJson('/api/logout', [], $this->bearer($token))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue(Token::query()->firstOrFail()->revoked);
    }

    public function test_revoked_token_cannot_be_reused(): void
    {
        $token = $this->loginAs(User::factory()->create());

        $this->postJson('/api/logout', [], $this->bearer($token))->assertOk();

        $this->app['auth']->forgetGuards();
        $this->getJson('/api/me', $this->bearer($token))->assertStatus(401);
    }
}
