<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ConsolidatedOrder;
use App\Services\ConsolidatedOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsolidatedOrderTest extends TestCase
{
    use RefreshDatabase;

    protected ConsolidatedOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConsolidatedOrderService();
    }

    public function test_can_populate_consolidated_orders()
    {
        // Create test data
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        // Populate consolidated orders
        $recordsProcessed = $this->service->populateConsolidatedOrders();

        // Assert
        $this->assertEquals(1, $recordsProcessed);
        $this->assertEquals(1, ConsolidatedOrder::count());

        $consolidatedOrder = ConsolidatedOrder::first();
        $this->assertEquals($order->id, $consolidatedOrder->order_id);
        $this->assertEquals($customer->name, $consolidatedOrder->customer_name);
        $this->assertEquals($product->name, $consolidatedOrder->product_name);
    }

    public function test_api_endpoints_work()
    {
        // Create test data
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        // Test populate endpoint
        $response = $this->postJson('/api/consolidated-orders/populate');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Test analytics endpoint
        $response = $this->getJson('/api/consolidated-orders');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_analytics_data_structure()
    {
        // Create test data
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        // Populate consolidated orders
        $this->service->populateConsolidatedOrders();

        // Get analytics data
        $analytics = $this->service->getAnalyticsData();

        // Assert that analytics structure is correct and has data
        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('total_revenue', $analytics);
        $this->assertArrayHasKey('total_orders', $analytics);
        $this->assertArrayHasKey('total_items', $analytics);
        $this->assertArrayHasKey('avg_order_value', $analytics);
        $this->assertArrayHasKey('top_products', $analytics);
        $this->assertArrayHasKey('revenue_by_month', $analytics);

        // Assert that we have some data
        $this->assertGreaterThan(0, $analytics['total_revenue']);
        $this->assertGreaterThan(0, $analytics['total_orders']);
        $this->assertGreaterThan(0, $analytics['total_items']);
    }
}
