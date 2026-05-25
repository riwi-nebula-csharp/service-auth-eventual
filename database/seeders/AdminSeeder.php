<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'          => 'Administrador Nebula',
            'email'         => 'nebula.admin.riwi@gmail.com',
            'password_hash' => Hash::make('nebula2000'),
            'provider'      => 'local',
            'role'          => 'admin',
            'status'        => 'active',
        ]);
    }
}