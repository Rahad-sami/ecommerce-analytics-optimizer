<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'price' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
