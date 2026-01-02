<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        DB::table('permissions')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_permissions')->delete();

        $defaultPermissions = Permission::defaultPermissions();
        Schema::disableForeignKeyConstraints();
        foreach ($defaultPermissions as $perm) {
            Permission::firstOrCreate($perm);
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $admin = Admin::first();
        $permissions = Permission::where('guard_name', 'admin')->get();
        $role = Role::first();
        $role->givePermissionTo($permissions);
        $admin->assignRole($role);
        $admins = Admin::find(1);
        $admins->assignRole($role);
        Schema::enableForeignKeyConstraints();
    }
}
