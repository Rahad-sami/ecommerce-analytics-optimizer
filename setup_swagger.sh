#!/bin/bash

echo "Setting up Swagger API Documentation..."

# Install Swagger package
echo "Installing L5-Swagger package..."
composer require darkaonline/l5-swagger

# Publish Swagger config
echo "Publishing Swagger configuration..."
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Generate Swagger documentation
echo "Generating Swagger documentation..."
php artisan l5-swagger:generate

echo "Swagger setup completed!"
echo ""
echo "Access Swagger documentation at:"
echo "http://127.0.0.1:8000/api/documentation"
echo ""
echo "To regenerate documentation after changes:"
echo "php artisan swagger:generate"