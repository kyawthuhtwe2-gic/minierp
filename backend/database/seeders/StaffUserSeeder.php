<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class StaffUserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'Staff@123456';

    public function run(): void
    {
        $email = env('STAFF_EMAIL', 'staff@example.com');
        $password = env('STAFF_PASSWORD', self::DEFAULT_PASSWORD);

        if (app()->isProduction() && $password === self::DEFAULT_PASSWORD) {
            throw new RuntimeException('Set STAFF_PASSWORD in .env before seeding the staff user in production.');
        }

        $exists = User::query()->where('email', $email)->exists();

        $values = ['name' => 'Staff User', 'role' => 'staff', 'email_verified_at' => now()];

        // Password only on first creation; re-running never resets a changed password.
        if (! $exists) {
            $values['password'] = Hash::make($password);
        }

        Model::unguarded(fn () => User::updateOrCreate(['email' => $email], $values));
    }
}
