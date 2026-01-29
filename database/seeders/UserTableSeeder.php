<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(UserService $userService): void
    {
        // Create sample doctor
      $userService->create([
            'name' => 'Dr. Ahmed Mohamed',
            'type' => UserTypeEnum::DOCTOR->value,
            'email' => 'doctor@example.com',
            'phone' => '01234567890',
            'password' => Hash::make('password'),
            'lat' => 30.044420,
            'long' => 31.235712,
            'map_description' => 'Cairo, Egypt',
            'note' => null,
            'is_active' => true,
            'is_blocked' => false,
        ]);

        // Create sample pharmacy
        $userService->create([
            'name' => 'Al-Shifa Pharmacy',
            'type' => UserTypeEnum::PHARMACY->value,
            'email' => 'pharmacy@example.com',
            'phone' => '01234567891',
            'password' => Hash::make('password'),
            'lat' => 31.200091,
            'long' => 29.918739,
            'map_description' => 'Alexandria, Egypt',
            'note' => 'Main pharmacy location',
            'is_active' => true,
            'is_blocked' => false,
        ]);
    }
}
