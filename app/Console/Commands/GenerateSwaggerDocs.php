<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSwaggerDocs extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate Swagger API documentation';

    public function handle(): int
    {
        $this->info('Generating Swagger documentation...');

        try {
            $this->call('l5-swagger:generate');
            $this->info('Swagger documentation generated successfully!');
            $this->info('Access documentation at: http://127.0.0.1:8000/api/documentation');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating Swagger documentation: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
