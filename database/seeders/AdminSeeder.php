<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['username' => 'CCS-FCO OFFICER'],
            [
                'name' => 'CCS-FCO OFFICER',
                'email' => 'admin@ccs.essu.edu.ph', // Placeholder since it's required in some setups but we use username
                'password' => \Illuminate\Support\Facades\Hash::make('ccsattendanceqrc-2026'),
                'role' => 'super_admin',
            ]
        );
    }
}
