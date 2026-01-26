<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        // Create sample orders for proof of concept
        foreach ($customers->take(50) as $customer) {
            // Each customer gets 1-5 orders
            $orderCount = rand(1, 5);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'order_date' => now()->subDays(rand(1, 365)),
                    'status' => $statuses[array_rand($statuses)],
                    'total_amount' => 0, // Will be calculated after items
                ]);

                // Each order gets 1-4 items
                $itemCount = rand(1, 4);
                $totalAmount = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $price = $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $totalAmount += $price * $quantity;
                }

                // Update order total
                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }
}
