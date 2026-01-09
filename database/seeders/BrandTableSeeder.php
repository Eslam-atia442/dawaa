<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => [
                    'ar' => 'نوفارتيس',
                    'en' => 'Novartis'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'فايزر',
                    'en' => 'Pfizer'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'جلاكسو سميث كلاين',
                    'en' => 'GlaxoSmithKline'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'سانوفي',
                    'en' => 'Sanofi'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'أسترازينيكا',
                    'en' => 'AstraZeneca'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'ميرك',
                    'en' => 'Merck'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'جونسون آند جونسون',
                    'en' => 'Johnson & Johnson'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'روش',
                    'en' => 'Roche'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'أبوت',
                    'en' => 'Abbott'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'إيلي ليلي',
                    'en' => 'Eli Lilly'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }
    }

}
