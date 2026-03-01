<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->unique()->words(3, true),
                'ar' => $this->faker->unique()->words(3, true),
            ],
            'description' => [
                'en' => $this->faker->paragraph(),
                'ar' => $this->faker->paragraph(),
            ],
            'store_id' => Store::inRandomOrder()->first()?->id ?? Store::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? Brand::factory(),
            'parent_id' => null,
            'price' => null,
            'quantity' => null,
            'expiry_date' => null,
            'production_line_number' => null,
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Create a child product state.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => null,
            'description' => null,
            'store_id' => null,
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => Product::whereNull('parent_id')->inRandomOrder()->first()?->id ?? Product::factory(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'expiry_date' => $this->faker->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
            'production_line_number' => $this->faker->unique()->bothify('PL-####-??'),
        ]);
    }
}
