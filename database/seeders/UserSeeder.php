<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Administrator',
            'initial' => 'ADM',
            'email' => 'jodoin@sami.co.id',
            'email_verified_at' => \Carbon\Carbon::now(),
            'password' => \Illuminate\Support\Facades\Hash::make('Jodoin@2024'),
            'status' => 1
        ]);

        $admin->syncRoles(['Administrator']);
    }
}
