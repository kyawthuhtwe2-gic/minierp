<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\StaffUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class StaffUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent_and_creates_staff(): void
    {
        $this->seed(StaffUserSeeder::class);
        $this->seed(StaffUserSeeder::class);

        $this->assertSame(1, User::where('email', 'staff@example.com')->count());
        $this->assertSame('staff', User::where('email', 'staff@example.com')->first()->role);
    }

    public function test_seeded_staff_reaches_staff_but_not_admin(): void
    {
        app(ClientRepository::class)->createPersonalAccessGrantClient('Test', 'users');
        $this->seed(StaffUserSeeder::class);

        $token = $this->postJson('/api/login', ['email' => 'staff@example.com', 'password' => 'Staff@123456'])
            ->assertOk()
            ->assertJsonPath('user.role', 'staff')
            ->json('access_token');

        $h = ['Authorization' => 'Bearer '.$token];
        $this->getJson('/api/staff/dashboard', $h)->assertOk();
        $this->getJson('/api/admin/dashboard', $h)->assertStatus(403);
        $this->getJson('/api/management', $h)->assertStatus(403);
    }
}
