<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $user = \App\Models\User::create([
            'name' => 'Talent User',
            'email' => 'talent@example.com',
            'password' => Hash::make('password'),
            'role' => 'talent'
        ]);
        $user->markEmailAsVerified();

        $user = \App\Models\User::create([
            'name' => 'Talent User 2',
            'email' => 'talent2@example.com',
            'password' => Hash::make('password'),
            'role' => 'talent'
        ]);
        $user->markEmailAsVerified();

        $user = \App\Models\User::create([
            'name' => 'Talent User 3',
            'email' => 'talent3@example.com',
            'password' => Hash::make('password'),
            'role' => 'talent'
        ]);
        $user->markEmailAsVerified();

        $user = \App\Models\User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);
        $user->markEmailAsVerified();
    }
}
