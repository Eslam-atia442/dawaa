<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent products
        $parentProducts = Product::factory()->count(20)->create();

        // Create child products for each parent product
        foreach ($parentProducts as $parentProduct) {
            // Create between 1-5 child products per parent
            $childCount = fake()->numberBetween(1, 5);

            Product::factory()
                ->count($childCount)
                ->child()
                ->create([
                    'parent_id' => $parentProduct->id,
                ]);
        }

        // Create some parent products with no children
        Product::factory()->count(5)->create();

        // Create some standalone child products (orphans for testing edge cases)
        Product::factory()->count(3)->child()->create();
    }
}
