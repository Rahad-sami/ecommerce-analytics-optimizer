<?php

namespace App\Console\Commands;

use App\Imports\ConsolidatedOrdersImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportConsolidatedOrders extends Command
{
    protected $signature = 'orders:import {file : Excel file path to import}';
    protected $description = 'Import consolidated orders from Excel file';

    public function handle(): int
    {
        $filepath = $this->argument('file');

        if (!file_exists($filepath)) {
            $this->error("File not found: {$filepath}");
            return Command::FAILURE;
        }

        $this->info('Starting import of consolidated orders...');

        try {
            Excel::import(new ConsolidatedOrdersImport, $filepath);

            $this->info('Import completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error importing data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
