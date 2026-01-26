<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('customer_id');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->bigInteger('product_id');
            $table->string('product_name');
            $table->string('sku', 100);
            $table->integer('quantity');
            $table->decimal('item_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->datetime('order_date');
            $table->string('order_status', 50);
            $table->decimal('order_total', 10, 2);
            $table->timestamps();

            // Optimized indexes for analytics queries
            $table->index(['order_date', 'order_status']);
            $table->index(['customer_id', 'order_date']);
            $table->index(['product_id', 'order_date']);
            $table->index('sku');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_orders');
    }
};
