<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'ar' => 'أدوية القلب والأوعية الدموية',
                    'en' => 'Cardiovascular Medications'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'المضادات الحيوية',
                    'en' => 'Antibiotics'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'مسكنات الألم',
                    'en' => 'Pain Relievers'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'الفيتامينات والمكملات',
                    'en' => 'Vitamins & Supplements'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'أدوية الجهاز الهضمي',
                    'en' => 'Digestive System Medications'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'أدوية الجهاز التنفسي',
                    'en' => 'Respiratory Medications'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'مستحضرات التجميل',
                    'en' => 'Cosmetics'
                ],
                'is_active' => true,
            ],
            [
                'name' => [
                    'ar' => 'منتجات العناية بالبشرة',
                    'en' => 'Skincare Products'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }

}
