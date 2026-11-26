<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'Admin@123456';

    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', self::DEFAULT_PASSWORD);

        if (app()->isProduction() && $password === self::DEFAULT_PASSWORD) {
            throw new RuntimeException('Set ADMIN_PASSWORD in .env before seeding the admin in production.');
        }

        $exists = User::query()->where('email', $email)->exists();

        $values = ['name' => 'Administrator', 'role' => 'admin', 'email_verified_at' => now()];

        // Set password only on first creation so re-running never resets a changed password.
        if (! $exists) {
            $values['password'] = Hash::make($password);
        }

        // `role` is intentionally not mass-assignable; unguard for this trusted seeder only.
        Model::unguarded(fn () => User::updateOrCreate(['email' => $email], $values));
    }
}
