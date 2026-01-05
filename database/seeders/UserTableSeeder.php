<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample doctor
        User::create([
            'name' => 'Dr. Ahmed Mohamed',
            'type' => UserTypeEnum::DOCTOR->value,
            'email' => 'doctor@example.com',
            'phone' => '01234567890',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_blocked' => false,
        ]);

        // Create sample pharmacy
        User::create([
            'name' => 'Al-Shifa Pharmacy',
            'type' => UserTypeEnum::PHARMACY->value,
            'email' => 'pharmacy@example.com',
            'phone' => '01234567891',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_blocked' => false,
        ]);
    }

}
