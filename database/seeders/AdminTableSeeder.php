<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Repositories\SQL\AdminRepository;
use App\Services\AdminService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(AdminService $adminService): void
    {
        $admin = $adminService->create(
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'phone' => '12345678910',
                'password' => Hash::make('123456'), // password
                'gender' => GenderEnum::MALE->value,
                'email_verified_at' => now(),
                'is_active' => 1,
            ]
        );

        Admin::factory()->count(10)->create();


    }
}
