<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample products for proof of concept
        $products = [
            ['name' => 'Laptop Pro', 'sku' => 'LAP001', 'price' => 1299.99],
            ['name' => 'Wireless Mouse', 'sku' => 'MOU001', 'price' => 29.99],
            ['name' => 'Mechanical Keyboard', 'sku' => 'KEY001', 'price' => 149.99],
            ['name' => 'Monitor 27"', 'sku' => 'MON001', 'price' => 399.99],
            ['name' => 'USB-C Hub', 'sku' => 'HUB001', 'price' => 79.99],
            ['name' => 'Webcam HD', 'sku' => 'CAM001', 'price' => 89.99],
            ['name' => 'Headphones', 'sku' => 'HEAD001', 'price' => 199.99],
            ['name' => 'Desk Lamp', 'sku' => 'LAMP001', 'price' => 49.99],
            ['name' => 'Phone Stand', 'sku' => 'STAND001', 'price' => 19.99],
            ['name' => 'Cable Organizer', 'sku' => 'ORG001', 'price' => 15.99],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create additional random products for testing
        Product::factory(90)->create();
    }
}
