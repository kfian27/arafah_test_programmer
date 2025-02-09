<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PharmacistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Pharmacist',
            'email' => 'pharmacist@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'pharmacist',
        ]);
    }
}
