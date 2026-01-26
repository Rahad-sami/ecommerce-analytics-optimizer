<?php

namespace App\Jobs;

use App\Services\ConsolidatedOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshConsolidatedOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large datasets

    public function handle(ConsolidatedOrderService $service): void
    {
        Log::info('Starting scheduled refresh of consolidated orders');

        try {
            $recordsProcessed = $service->refreshConsolidatedOrders();

            Log::info("Scheduled refresh completed successfully. Records processed: {$recordsProcessed}");
        } catch (\Exception $e) {
            Log::error('Scheduled refresh failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
