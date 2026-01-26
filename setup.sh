#!/bin/bash

echo "Setting up E-commerce Database Optimization Solution..."

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate

# Seed the database
echo "Seeding database with sample data..."
php artisan db:seed

# Populate consolidated orders
echo "Populating consolidated orders table..."
php artisan orders:consolidate

# Create storage directories
echo "Creating storage directories..."
mkdir -p storage/app/exports
mkdir -p storage/app/imports

echo "Setup completed successfully!"
echo ""
echo "Available commands:"
echo "  php artisan orders:consolidate          - Populate consolidated orders"
echo "  php artisan orders:export               - Export to Excel"
echo "  php artisan orders:import <file>        - Import from Excel"
echo "  php artisan swagger:generate             - Generate API documentation"
echo "  php artisan test                        - Run tests"
echo ""
echo "API endpoints available at:"
echo "  GET  /api/consolidated-orders           - Analytics data"
echo "  POST /api/consolidated-orders/populate  - Trigger population"
echo "  GET  /api/consolidated-orders/export    - Export Excel"
echo "  POST /api/consolidated-orders/import    - Import Excel"
echo ""
echo "Swagger API Documentation:"
echo "  http://127.0.0.1:8000/api/documentation"