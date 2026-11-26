<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_admin_with_hashed_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Administrator', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertNotSame('Admin@123456', $admin->password);
        $this->assertTrue(Hash::check('Admin@123456', $admin->password));
    }

    public function test_seeder_is_idempotent_and_keeps_changed_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        User::where('email', 'admin@example.com')->firstOrFail()
            ->forceFill(['password' => 'Changed@123'])->save();

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
        $this->assertTrue(Hash::check('Changed@123', User::where('email', 'admin@example.com')->first()->password));
    }

    public function test_seeder_preserves_other_users(): void
    {
        $other = User::factory()->role('staff')->create();

        $this->seed(AdminUserSeeder::class);

        $this->assertSame('staff', $other->fresh()->role);
        $this->assertSame(2, User::count());
    }

    public function test_seeded_admin_can_login_and_reach_admin_endpoint(): void
    {
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test', 'users');
        $this->seed(AdminUserSeeder::class);

        $res = $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'Admin@123456'])
            ->assertOk()
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonMissingPath('user.password');

        $this->getJson('/api/admin/dashboard', ['Authorization' => 'Bearer '.$res->json('access_token')])
            ->assertOk();
    }
}
