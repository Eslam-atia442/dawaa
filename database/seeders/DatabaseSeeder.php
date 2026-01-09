<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void{
        $this->call([
            RolesTableSeeder::class,
            UserTableSeeder::class,
            AdminTableSeeder::class,
            PermissionsTableSeeder::class,
            SettingTableSeeder::class,
            CountryTableSeeder::class,
            RegionTableSeeder::class,
            CityTableSeeder::class,
            StoreTableSeeder::class,
            CategoryTableSeeder::class,
            BrandTableSeeder::class,
        ]);
    }
}
