<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('STAFF_NAME');
        $email = env('STAFF_EMAIL');
        $password = env('STAFF_PASSWORD');
        $role = env('STAFF_ROLE');

        if (app()->isProduction() && (!$name || ! $email || ! $password || ! $role)) {
            throw new RuntimeException('Set STAFF_NAME, STAFF_EMAIL, STAFF_PASSWORD, and STAFF_ROLE in .env before seeding the staff user in production.');
        }

        $exists = User::query()->where('email', $email)->exists();

        $values = ['name' => $name, 'role' => $role, 'email_verified_at' => now()];

        // Password only on first creation; re-running never resets a changed password.
        if (! $exists) {
            $values['password'] = Hash::make($password);
        }

        Model::unguarded(fn () => User::updateOrCreate(['email' => $email], $values));
    }
}
