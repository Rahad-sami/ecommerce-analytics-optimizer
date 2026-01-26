<?php

namespace App\Console\Commands;

use App\Services\ConsolidatedOrderService;
use Illuminate\Console\Command;

class PopulateConsolidatedOrders extends Command
{
    protected $signature = 'orders:consolidate {--refresh : Refresh existing data}';
    protected $description = 'Populate consolidated_orders table with denormalized data';

    public function handle(ConsolidatedOrderService $service): int
    {
        $this->info('Starting consolidated orders population...');

        $startTime = microtime(true);

        try {
            if ($this->option('refresh')) {
                $recordsProcessed = $service->refreshConsolidatedOrders();
            } else {
                $recordsProcessed = $service->populateConsolidatedOrders();
            }

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->info("Successfully processed {$recordsProcessed} records in {$executionTime} seconds");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error populating consolidated orders: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
