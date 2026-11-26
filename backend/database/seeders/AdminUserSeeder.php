<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME',);
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $role = env('ADMIN_ROLE');

        if (app()->isProduction() && (!$name || ! $email || ! $password || ! $role)) {
            throw new RuntimeException('Set ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD, and ADMIN_ROLE in .env before seeding the admin in production.');
        }

        $exists = User::query()->where('email', $email)->exists();

        $values = ['name' => $name, 'role' => $role, 'email_verified_at' => now()];

        // Set password only on first creation so re-running never resets a changed password.
        if (! $exists) {
            $values['password'] = Hash::make($password);
        }

        // `role` is intentionally not mass-assignable; unguard for this trusted seeder only.
        Model::unguarded(fn () => User::updateOrCreate(['email' => $email], $values));
    }
}
