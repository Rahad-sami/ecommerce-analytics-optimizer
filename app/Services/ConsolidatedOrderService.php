<?php

namespace App\Services;

use App\Models\ConsolidatedOrder;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsolidatedOrderService
{
    /**
     * Populate consolidated_orders table with optimized batch processing
     */
    public function populateConsolidatedOrders(): int
    {
        Log::info('Starting consolidated orders population');

        // Clear existing data
        ConsolidatedOrder::truncate();

        $batchSize = 1000;
        $totalProcessed = 0;

        // Use chunked processing to handle large datasets efficiently
        OrderItem::with(['order.customer', 'product'])
            ->chunk($batchSize, function ($orderItems) use (&$totalProcessed) {
                $consolidatedData = [];

                foreach ($orderItems as $item) {
                    $order = $item->order;
                    $customer = $order->customer;
                    $product = $item->product;

                    $consolidatedData[] = [
                        'order_id' => $order->id,
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'customer_email' => $customer->email,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $item->quantity,
                        'item_price' => $item->price,
                        'line_total' => $item->price * $item->quantity,
                        'order_date' => $order->order_date,
                        'order_status' => $order->status,
                        'order_total' => $order->total_amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Batch insert for better performance
                ConsolidatedOrder::insert($consolidatedData);
                $totalProcessed += count($consolidatedData);

                Log::info("Processed batch: {$totalProcessed} records");
            });

        Log::info("Consolidated orders population completed. Total records: {$totalProcessed}");

        return $totalProcessed;
    }

    /**
     * Refresh consolidated orders data (for scheduled job)
     */
    public function refreshConsolidatedOrders(): int
    {
        Log::info('Starting consolidated orders refresh');

        return $this->populateConsolidatedOrders();
    }

    /**
     * Get analytics data with optimized queries
     */
    public function getAnalyticsData(array $filters = []): array
    {
        $query = ConsolidatedOrder::query();

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('order_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('order_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['status'])) {
            $query->where('order_status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return [
            'total_revenue' => $query->sum('line_total'),
            'total_orders' => $query->distinct('order_id')->count(),
            'total_items' => $query->sum('quantity'),
            'avg_order_value' => $query->avg('order_total'),
            'top_products' => $this->getTopProducts($query->clone()),
            'revenue_by_month' => $this->getRevenueByMonth($query->clone()),
        ];
    }

    private function getTopProducts($query)
    {
        return $query->select('product_name', 'sku')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(line_total) as total_revenue')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    private function getRevenueByMonth($query)
    {
        return $query->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month')
            ->selectRaw('SUM(line_total) as revenue')
            ->selectRaw('COUNT(DISTINCT order_id) as orders')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
