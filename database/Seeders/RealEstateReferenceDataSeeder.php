<?php

namespace TallCms\RealEstate\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use TallCms\RealEstate\Models\PropertyType;
use TallCms\RealEstate\Models\District;
use TallCms\RealEstate\Models\Amenity;
use TallCms\RealEstate\Models\Feature;

class RealEstateReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPropertyTypes();
        $this->seedSingaporeDistricts();
        $this->seedAmenities();
        $this->seedFeatures();
    }

    /**
     * Seed property types
     */
    protected function seedPropertyTypes(): void
    {
        $propertyTypes = [
            [
                'name' => 'HDB Flat',
                'slug' => 'hdb-flat',
                'description' => 'Housing Development Board apartments',
                'icon' => 'heroicon-o-building-office',
                'sort_order' => 1,
            ],
            [
                'name' => 'Condominium',
                'slug' => 'condominium',
                'description' => 'Private residential condominium',
                'icon' => 'heroicon-o-building-office-2',
                'sort_order' => 2,
            ],
            [
                'name' => 'Landed House',
                'slug' => 'landed-house',
                'description' => 'Terrace, semi-detached, or detached house',
                'icon' => 'heroicon-o-home',
                'sort_order' => 3,
            ],
            [
                'name' => 'Executive Condominium',
                'slug' => 'executive-condominium',
                'description' => 'Executive condominium (EC)',
                'icon' => 'heroicon-o-building-storefront',
                'sort_order' => 4,
            ],
            [
                'name' => 'Commercial',
                'slug' => 'commercial',
                'description' => 'Office space, retail, or commercial property',
                'icon' => 'heroicon-o-briefcase',
                'sort_order' => 5,
            ],
            [
                'name' => 'Industrial',
                'slug' => 'industrial',
                'description' => 'Industrial property or warehouse',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'sort_order' => 6,
            ],
        ];

        foreach ($propertyTypes as $type) {
            PropertyType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }

    /**
     * Seed Singapore districts
     */
    protected function seedSingaporeDistricts(): void
    {
        $districts = [
            // District 1
            ['name' => 'Boat Quay', 'slug' => 'boat-quay', 'country' => 'Singapore', 'postal_code_prefix' => '01', 'sort_order' => 1],
            ['name' => 'Chinatown', 'slug' => 'chinatown', 'country' => 'Singapore', 'postal_code_prefix' => '01', 'sort_order' => 2],
            ['name' => 'Raffles Place', 'slug' => 'raffles-place', 'country' => 'Singapore', 'postal_code_prefix' => '01', 'sort_order' => 3],

            // District 2
            ['name' => 'Anson', 'slug' => 'anson', 'country' => 'Singapore', 'postal_code_prefix' => '02', 'sort_order' => 4],
            ['name' => 'Tanjong Pagar', 'slug' => 'tanjong-pagar', 'country' => 'Singapore', 'postal_code_prefix' => '02', 'sort_order' => 5],

            // District 3
            ['name' => 'Alexandra', 'slug' => 'alexandra', 'country' => 'Singapore', 'postal_code_prefix' => '03', 'sort_order' => 6],
            ['name' => 'Commonwealth', 'slug' => 'commonwealth', 'country' => 'Singapore', 'postal_code_prefix' => '03', 'sort_order' => 7],

            // District 9
            ['name' => 'Orchard', 'slug' => 'orchard', 'country' => 'Singapore', 'postal_code_prefix' => '09', 'sort_order' => 8],
            ['name' => 'River Valley', 'slug' => 'river-valley', 'country' => 'Singapore', 'postal_code_prefix' => '09', 'sort_order' => 9],

            // District 10
            ['name' => 'Ardmore', 'slug' => 'ardmore', 'country' => 'Singapore', 'postal_code_prefix' => '10', 'sort_order' => 10],
            ['name' => 'Bukit Timah', 'slug' => 'bukit-timah', 'country' => 'Singapore', 'postal_code_prefix' => '10', 'sort_order' => 11],
            ['name' => 'Holland Road', 'slug' => 'holland-road', 'country' => 'Singapore', 'postal_code_prefix' => '10', 'sort_order' => 12],

            // District 11
            ['name' => 'Novena', 'slug' => 'novena', 'country' => 'Singapore', 'postal_code_prefix' => '11', 'sort_order' => 13],
            ['name' => 'Newton', 'slug' => 'newton', 'country' => 'Singapore', 'postal_code_prefix' => '11', 'sort_order' => 14],

            // Popular residential areas
            ['name' => 'Clementi', 'slug' => 'clementi', 'country' => 'Singapore', 'postal_code_prefix' => '12', 'sort_order' => 15],
            ['name' => 'Jurong East', 'slug' => 'jurong-east', 'country' => 'Singapore', 'postal_code_prefix' => '60', 'sort_order' => 16],
            ['name' => 'Tampines', 'slug' => 'tampines', 'country' => 'Singapore', 'postal_code_prefix' => '52', 'sort_order' => 17],
            ['name' => 'Bedok', 'slug' => 'bedok', 'country' => 'Singapore', 'postal_code_prefix' => '46', 'sort_order' => 18],
            ['name' => 'Hougang', 'slug' => 'hougang', 'country' => 'Singapore', 'postal_code_prefix' => '53', 'sort_order' => 19],
            ['name' => 'Ang Mo Kio', 'slug' => 'ang-mo-kio', 'country' => 'Singapore', 'postal_code_prefix' => '56', 'sort_order' => 20],
        ];

        foreach ($districts as $district) {
            District::firstOrCreate(['slug' => $district['slug']], $district);
        }
    }

    /**
     * Seed amenities
     */
    protected function seedAmenities(): void
    {
        $amenities = [
            // Building amenities
            ['name' => 'Swimming Pool', 'slug' => 'swimming-pool', 'category' => 'building', 'icon' => '🏊', 'sort_order' => 1],
            ['name' => 'Gym/Fitness Center', 'slug' => 'gym-fitness-center', 'category' => 'building', 'icon' => '💪', 'sort_order' => 2],
            ['name' => 'Tennis Court', 'slug' => 'tennis-court', 'category' => 'building', 'icon' => '🎾', 'sort_order' => 3],
            ['name' => 'Basketball Court', 'slug' => 'basketball-court', 'category' => 'building', 'icon' => '🏀', 'sort_order' => 4],
            ['name' => 'Playground', 'slug' => 'playground', 'category' => 'building', 'icon' => '🛝', 'sort_order' => 5],
            ['name' => 'BBQ Pits', 'slug' => 'bbq-pits', 'category' => 'building', 'icon' => '🔥', 'sort_order' => 6],
            ['name' => 'Function Room', 'slug' => 'function-room', 'category' => 'building', 'icon' => '🏢', 'sort_order' => 7],
            ['name' => 'Concierge', 'slug' => 'concierge', 'category' => 'building', 'icon' => '🎩', 'sort_order' => 8],

            // Location benefits
            ['name' => 'Near MRT', 'slug' => 'near-mrt', 'category' => 'location', 'icon' => '🚇', 'sort_order' => 9],
            ['name' => 'Near Shopping Mall', 'slug' => 'near-shopping-mall', 'category' => 'location', 'icon' => '🛒', 'sort_order' => 10],
            ['name' => 'Near Schools', 'slug' => 'near-schools', 'category' => 'location', 'icon' => '🏫', 'sort_order' => 11],
            ['name' => 'Near Hospital', 'slug' => 'near-hospital', 'category' => 'location', 'icon' => '🏥', 'sort_order' => 12],
            ['name' => 'Near Park', 'slug' => 'near-park', 'category' => 'location', 'icon' => '🌳', 'sort_order' => 13],

            // Security features
            ['name' => '24/7 Security', 'slug' => '24-7-security', 'category' => 'security', 'icon' => '🛡️', 'sort_order' => 14],
            ['name' => 'CCTV', 'slug' => 'cctv', 'category' => 'security', 'icon' => '📹', 'sort_order' => 15],
            ['name' => 'Access Card System', 'slug' => 'access-card-system', 'category' => 'security', 'icon' => '🔑', 'sort_order' => 16],

            // Parking & Transportation
            ['name' => 'Covered Parking', 'slug' => 'covered-parking', 'category' => 'parking', 'icon' => '🚗', 'sort_order' => 17],
            ['name' => 'Visitor Parking', 'slug' => 'visitor-parking', 'category' => 'parking', 'icon' => '🅿️', 'sort_order' => 18],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(['slug' => $amenity['slug']], $amenity);
        }
    }

    /**
     * Seed features
     */
    protected function seedFeatures(): void
    {
        $features = [
            // Interior features
            ['name' => 'Air Conditioning', 'slug' => 'air-conditioning', 'category' => 'interior', 'icon' => '❄️', 'sort_order' => 1],
            ['name' => 'Built-in Wardrobes', 'slug' => 'built-in-wardrobes', 'category' => 'interior', 'icon' => '👗', 'sort_order' => 2],
            ['name' => 'Study Room', 'slug' => 'study-room', 'category' => 'interior', 'icon' => '📚', 'sort_order' => 3],
            ['name' => 'Maid Room', 'slug' => 'maid-room', 'category' => 'interior', 'icon' => '🏠', 'sort_order' => 4],
            ['name' => 'High Ceiling', 'slug' => 'high-ceiling', 'category' => 'interior', 'icon' => '⬆️', 'sort_order' => 5],

            // Kitchen features
            ['name' => 'Open Kitchen', 'slug' => 'open-kitchen', 'category' => 'kitchen', 'icon' => '🍳', 'sort_order' => 6],
            ['name' => 'Kitchen Island', 'slug' => 'kitchen-island', 'category' => 'kitchen', 'icon' => '🏝️', 'sort_order' => 7],
            ['name' => 'Built-in Appliances', 'slug' => 'built-in-appliances', 'category' => 'kitchen', 'icon' => '🔧', 'sort_order' => 8],

            // Bathroom features
            ['name' => 'Jacuzzi', 'slug' => 'jacuzzi', 'category' => 'bathroom', 'icon' => '🛁', 'sort_order' => 9],
            ['name' => 'Shower Stall', 'slug' => 'shower-stall', 'category' => 'bathroom', 'icon' => '🚿', 'sort_order' => 10],
            ['name' => 'Bathtub', 'slug' => 'bathtub', 'category' => 'bathroom', 'icon' => '🛀', 'sort_order' => 11],

            // Exterior features
            ['name' => 'Balcony', 'slug' => 'balcony', 'category' => 'exterior', 'icon' => '🌅', 'sort_order' => 12],
            ['name' => 'Terrace', 'slug' => 'terrace', 'category' => 'exterior', 'icon' => '🏡', 'sort_order' => 13],
            ['name' => 'Garden', 'slug' => 'garden', 'category' => 'exterior', 'icon' => '🌺', 'sort_order' => 14],
            ['name' => 'Pool View', 'slug' => 'pool-view', 'category' => 'exterior', 'icon' => '🏊', 'sort_order' => 15],
            ['name' => 'Sea View', 'slug' => 'sea-view', 'category' => 'exterior', 'icon' => '🌊', 'sort_order' => 16],
            ['name' => 'City View', 'slug' => 'city-view', 'category' => 'exterior', 'icon' => '🏙️', 'sort_order' => 17],

            // Flooring & Finishes
            ['name' => 'Parquet Flooring', 'slug' => 'parquet-flooring', 'category' => 'flooring', 'icon' => '🪵', 'sort_order' => 18],
            ['name' => 'Marble Flooring', 'slug' => 'marble-flooring', 'category' => 'flooring', 'icon' => '💎', 'sort_order' => 19],
            ['name' => 'Tiles', 'slug' => 'tiles', 'category' => 'flooring', 'icon' => '🟫', 'sort_order' => 20],

            // Utilities & Systems
            ['name' => 'Central Air', 'slug' => 'central-air', 'category' => 'utilities', 'icon' => '🌀', 'sort_order' => 21],
            ['name' => 'Water Heater', 'slug' => 'water-heater', 'category' => 'utilities', 'icon' => '♨️', 'sort_order' => 22],
        ];

        foreach ($features as $feature) {
            Feature::firstOrCreate(['slug' => $feature['slug']], $feature);
        }
    }
}