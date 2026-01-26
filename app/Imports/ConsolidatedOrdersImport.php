<?php

namespace App\Imports;

use App\Models\ConsolidatedOrder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class ConsolidatedOrdersImport implements ToModel, WithHeadingRow, WithChunkReading, WithValidation
{
    public function model(array $row)
    {
        Log::info('Importing row', $row);

        // Find existing record by order_id and product_id
        $existing = ConsolidatedOrder::where('order_id', $row['order_id'])
            ->where('product_id', $row['product_id'])
            ->first();

        $data = [
            'order_id' => $row['order_id'],
            'customer_id' => $row['customer_id'],
            'customer_name' => $row['customer_name'],
            'customer_email' => $row['customer_email'],
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'sku' => $row['sku'],
            'quantity' => $row['quantity'],
            'item_price' => $row['item_price'],
            'line_total' => $row['line_total'],
            'order_date' => $row['order_date'],
            'order_status' => $row['order_status'],
            'order_total' => $row['order_total'],
        ];

        if ($existing) {
            // Update existing record
            $existing->update($data);
            return null; // Don't create new model
        }

        // Create new record
        return new ConsolidatedOrder($data);
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'product_id' => 'required|integer',
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'item_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
            'order_date' => 'required|date',
            'order_status' => 'required|string|max:50',
            'order_total' => 'required|numeric|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
