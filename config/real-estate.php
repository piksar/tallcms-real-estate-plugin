<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Real Estate Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the TALL CMS Real Estate
    | plugin. You can customize various aspects of the plugin's behavior here.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure database table names and relationships for the plugin.
    |
    */
    'database' => [
        'table_prefix' => 'real_estate_',
        'properties_table' => 'real_estate_properties',
        'use_soft_deletes' => true,
        'timestamps' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search & Filtering Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default search and filtering behavior.
    |
    */
    'search' => [
        'default_per_page' => 12,
        'max_per_page' => 50,
        'enable_keywords_search' => true,
        'searchable_fields' => [
            'title',
            'description',
            'address',
            'city',
            'state',
            'zip_code',
            'agent_name',
            'meta_title',
            'meta_description',
        ],
        'default_sort' => 'latest', // latest, price_low, price_high, bedrooms, square_footage
    ],

    /*
    |--------------------------------------------------------------------------
    | Property Configuration
    |--------------------------------------------------------------------------
    |
    | Configure property-related settings.
    |
    */
    'properties' => [
        'enable_tenure_field' => true,
        'enable_agent_fields' => true,
        'enable_seo_fields' => true,
        'enable_coordinates' => true,
        'enable_virtual_tours' => true,
        'required_fields' => [
            'title',
            'property_type_id',
            'price',
            'address',
            'city',
        ],
        'image_storage' => [
            'disk' => 'public',
            'path' => 'properties',
            'max_size' => 2048, // KB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | Configure supported currencies and formatting.
    |
    */
    'currency' => [
        'default' => 'USD',
        'supported' => [
            'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
            'SGD' => ['symbol' => 'S$', 'name' => 'Singapore Dollar'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
            'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar'],
            'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar'],
        ],
        'formatting' => [
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the plugin appears in the Filament admin panel.
    |
    */
    'admin' => [
        'enabled' => true,
        'navigation_group' => 'Real Estate',
        'navigation_sort' => 100,
        'icons' => [
            'properties' => 'heroicon-o-building-office',
            'property_types' => 'heroicon-o-squares-2x2',
            'districts' => 'heroicon-o-map',
            'amenities' => 'heroicon-o-star',
            'features' => 'heroicon-o-check-badge',
        ],
        'resources' => [
            'property' => TallCms\RealEstate\Resources\PropertyResource::class,
            'property_type' => TallCms\RealEstate\Resources\PropertyTypeResource::class,
            'district' => TallCms\RealEstate\Resources\DistrictResource::class,
            'amenity' => TallCms\RealEstate\Resources\AmenityResource::class,
            'feature' => TallCms\RealEstate\Resources\FeatureResource::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Block Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the blocks provided by this plugin.
    |
    */
    'blocks' => [
        'property_search' => [
            'enabled' => true,
            'class' => TallCms\RealEstate\Blocks\PropertySearchBlock::class,
            'category' => 'Real Estate',
            'icon' => 'heroicon-o-magnifying-glass',
        ],
        'property_listings' => [
            'enabled' => true,
            'class' => TallCms\RealEstate\Blocks\PropertyListingsBlock::class,
            'category' => 'Real Estate',
            'icon' => 'heroicon-o-building-office-2',
        ],
        'featured_property' => [
            'enabled' => true,
            'class' => TallCms\RealEstate\Blocks\FeaturedPropertyBlock::class,
            'category' => 'Real Estate',
            'icon' => 'heroicon-o-star',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    |
    | Configure SEO-related settings for properties.
    |
    */
    'seo' => [
        'enable_meta_fields' => true,
        'auto_generate_meta' => true,
        'property_url_structure' => '/property/{slug}',
        'properties_page_url' => '/properties',
        'sitemap_integration' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configure integrations with external services.
    |
    */
    'integrations' => [
        'maps' => [
            'provider' => 'google', // google, mapbox, openstreetmap
            'api_key' => env('MAPS_API_KEY'),
            'default_zoom' => 15,
        ],
        'email_notifications' => [
            'enabled' => true,
            'contact_form_notifications' => true,
            'inquiry_notifications' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching and performance settings.
    |
    */
    'performance' => [
        'cache_search_results' => true,
        'cache_duration' => 3600, // seconds
        'enable_query_optimization' => true,
        'lazy_load_images' => true,
    ],
];
