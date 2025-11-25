<?php

namespace TallCms\RealEstate;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TallCms\RealEstate\Resources\PropertyResource;
use TallCms\RealEstate\Resources\PropertyTypeResource;
use TallCms\RealEstate\Resources\DistrictResource;
use TallCms\RealEstate\Resources\AmenityResource;
use TallCms\RealEstate\Resources\FeatureResource;

class RealEstatePlugin implements Plugin
{
    /**
     * Plugin configuration
     */
    protected bool $enablePropertyManagement = true;
    protected bool $enablePropertyBlocks = true;
    protected string $currency = 'USD';
    protected array $propertyTypes = [
        'house' => 'House',
        'apartment' => 'Apartment',
        'condo' => 'Condo',
        'townhouse' => 'Townhouse',
        'commercial' => 'Commercial',
        'land' => 'Land'
    ];
    protected array $listingStatuses = [
        'active' => 'Active',
        'pending' => 'Pending',
        'sold' => 'Sold',
        'rented' => 'Rented',
        'off_market' => 'Off Market'
    ];

    public function getId(): string
    {
        return 'real-estate';
    }

    public function register(Panel $panel): void
    {
        if ($this->enablePropertyManagement) {
            $panel->resources([
                // Main property management
                PropertyResource::class,
                
                // Reference data management
                PropertyTypeResource::class,
                DistrictResource::class,
                AmenityResource::class,
                FeatureResource::class,
            ]);
        }

        // Register additional pages if needed
        // $panel->pages([
        //     PropertyAnalyticsPage::class,
        // ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin initialization - blocks are registered via service provider
        logger()->info('Real Estate Plugin: Initialized for panel ' . $panel->getId());
    }

    /**
     * Configuration methods for fluent setup
     */
    public function propertyManagement(bool $enabled = true): static
    {
        $this->enablePropertyManagement = $enabled;
        return $this;
    }

    public function propertyBlocks(bool $enabled = true): static
    {
        $this->enablePropertyBlocks = $enabled;
        return $this;
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function propertyTypes(array $types): static
    {
        $this->propertyTypes = $types;
        return $this;
    }

    public function listingStatuses(array $statuses): static
    {
        $this->listingStatuses = $statuses;
        return $this;
    }

    /**
     * Getters for configuration values
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPropertyTypes(): array
    {
        return $this->propertyTypes;
    }

    public function getListingStatuses(): array
    {
        return $this->listingStatuses;
    }

    public function isPropertyManagementEnabled(): bool
    {
        return $this->enablePropertyManagement;
    }

    public function arePropertyBlocksEnabled(): bool
    {
        return $this->enablePropertyBlocks;
    }

    /**
     * Register property blocks with the block manager
     */
    protected function registerPropertyBlocks(): void
    {
        try {
            $blockManager = app(\App\TallCms\PageBuilder\BlockManager::class);
            
            // Register property search block
            $blockManager->register(
                \TallCms\RealEstate\Blocks\PropertySearchBlock::class
            );

            // Register property listings block
            $blockManager->register(
                \TallCms\RealEstate\Blocks\PropertyListingsBlock::class
            );

            // Register featured property block
            $blockManager->register(
                \TallCms\RealEstate\Blocks\FeaturedPropertyBlock::class
            );
        } catch (\Exception $e) {
            // Silently handle if block manager isn't ready yet
            // Blocks will be auto-discovered instead
            logger()->info('Real Estate Plugin: Block manager not ready, blocks will be auto-discovered', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Load plugin-specific views
     */
    protected function loadPluginViews(): void
    {
        view()->addNamespace('real-estate', base_path('app/Plugins/RealEstate/views'));
    }

    /**
     * Get plugin instance for configuration access
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Get current plugin configuration from container
     */
    public static function get(): static
    {
        if (app()->bound(static::class)) {
            return app(static::class);
        }
        
        // Return a new instance with default config if not bound
        return new static();
    }
}