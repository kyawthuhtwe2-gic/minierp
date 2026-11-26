<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class PersonalAccessClientSeeder extends Seeder
{
    public function run(): void
    {
        $exists = Client::query()->get()
            ->contains(fn (Client $c) => in_array('personal_access', $c->grant_types ?? [], true));

        if (! $exists) {
            app(ClientRepository::class)->createPersonalAccessGrantClient('MiniERP', 'users');
        }
    }
}
