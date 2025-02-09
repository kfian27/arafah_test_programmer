<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Doctor',
            'email' => 'doctor@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'doctor',
        ]);
    }
}
