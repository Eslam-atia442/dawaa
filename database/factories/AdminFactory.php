<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_at = $this->faker ->dateTimeBetween('-1 year', 'now');
        return [
            'name' =>  fake('ar_EG')->name(['50% male', '50% female']),
            'email' =>  fake()->unique()->freeEmail(),
            'phone' =>  fake()->phoneNumber(),
            'password' => Hash::make('123456') , // password
            'gender' =>  fake()->randomElement([GenderEnum::MALE->value, GenderEnum::FEMALE->value]),
            'email_verified_at' => $this->faker ->dateTimeBetween('-1 year', 'now'),
            'created_at' => $created_at,
            'updated_at' => $created_at,
            'is_active' =>  fake()->randomElement([0,1]),
        ];
    }
}
