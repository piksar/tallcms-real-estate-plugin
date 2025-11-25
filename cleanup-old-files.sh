#!/bin/bash

# Real Estate Plugin Cleanup Script
# This script removes Real Estate related files from the main Laravel directories
# after they have been moved to the plugin structure

echo "🧹 Real Estate Plugin Cleanup Script"
echo "====================================="
echo ""
echo "This script will remove Real Estate plugin files from main Laravel directories."
echo "Make sure you have backed up your database before running this!"
echo ""

read -p "Do you want to continue? (y/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Cleanup cancelled."
    exit 1
fi

echo ""
echo "🗃️  Removing old migration files..."

# Remove Real Estate migrations from main Laravel directory
rm -f database/migrations/*real_estate*.php
rm -f database/migrations/*properties*.php
rm -f database/migrations/*tenure*.php

echo "   ✅ Migration files removed"

echo ""
echo "🌱 Removing old seeder files..."

# Remove Real Estate seeders from main Laravel directory
rm -f database/seeders/RealEstateReferenceDataSeeder.php
rm -f database/seeders/PropertySeeder.php

echo "   ✅ Seeder files removed"

echo ""
echo "🎉 Cleanup complete!"
echo ""
echo "📝 Note: The plugin migrations and seeders are now located in:"
echo "   - app/Plugins/RealEstate/src/Database/Migrations/"
echo "   - app/Plugins/RealEstate/src/Database/Seeders/"
echo ""
echo "🚀 To install the plugin on a new site, use:"
echo "   php artisan real-estate:install"