<?php

namespace App\Console\Commands;

use App\Exports\ConsolidatedOrdersExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExportConsolidatedOrders extends Command
{
    protected $signature = 'orders:export 
                            {--file=consolidated_orders.xlsx : Output file name}
                            {--start-date= : Start date filter (Y-m-d)}
                            {--end-date= : End date filter (Y-m-d)}
                            {--status= : Order status filter}
                            {--customer-id= : Customer ID filter}';

    protected $description = 'Export consolidated orders to Excel file';

    public function handle(): int
    {
        $this->info('Starting export of consolidated orders...');

        $filters = array_filter([
            'start_date' => $this->option('start-date'),
            'end_date' => $this->option('end-date'),
            'status' => $this->option('status'),
            'customer_id' => $this->option('customer-id'),
        ]);

        $filename = $this->option('file');
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure exports directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        try {
            Excel::store(new ConsolidatedOrdersExport($filters), 'exports/' . $filename);

            $this->info("Export completed successfully!");
            $this->info("File saved to: {$filepath}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error exporting data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
