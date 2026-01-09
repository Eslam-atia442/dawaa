<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => [
                    'ar' => 'صيدلية النور',
                    'en' => 'Al-Noor Pharmacy'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'صيدلية الشفاء',
                    'en' => 'Al-Shifa Pharmacy'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'صيدلية الحياة',
                    'en' => 'Al-Hayat Pharmacy'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'صيدلية الأمل',
                    'en' => 'Al-Amal Pharmacy'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'صيدلية الصحة',
                    'en' => 'Al-Sihha Pharmacy'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }
    }

}
