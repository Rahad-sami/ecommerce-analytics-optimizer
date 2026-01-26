<?php

namespace App\Exports;

use App\Models\ConsolidatedOrder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ConsolidatedOrdersExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ConsolidatedOrder::query();

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->where('order_date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('order_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('order_status', $this->filters['status']);
        }

        if (!empty($this->filters['customer_id'])) {
            $query->where('customer_id', $this->filters['customer_id']);
        }

        return $query->orderBy('order_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer ID',
            'Customer Name',
            'Customer Email',
            'Product ID',
            'Product Name',
            'SKU',
            'Quantity',
            'Item Price',
            'Line Total',
            'Order Date',
            'Order Status',
            'Order Total',
        ];
    }

    public function map($consolidatedOrder): array
    {
        return [
            $consolidatedOrder->order_id,
            $consolidatedOrder->customer_id,
            $consolidatedOrder->customer_name,
            $consolidatedOrder->customer_email,
            $consolidatedOrder->product_id,
            $consolidatedOrder->product_name,
            $consolidatedOrder->sku,
            $consolidatedOrder->quantity,
            $consolidatedOrder->item_price,
            $consolidatedOrder->line_total,
            $consolidatedOrder->order_date->format('Y-m-d H:i:s'),
            $consolidatedOrder->order_status,
            $consolidatedOrder->order_total,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
