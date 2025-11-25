<?php

namespace TallCms\RealEstate;

use App\TallCms\PageBuilder\AbstractBlock;
use App\TallCms\PageBuilder\BlockManager;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TallCms\RealEstate\Commands\InstallRealEstateCommand;
use TallCms\RealEstate\Commands\VerifyRealEstateCommand;
use TallCms\RealEstate\Livewire\PropertySearchComponent;

class RealEstateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('real-estate')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_properties_table',
                'create_real_estate_property_types_table',
                'create_real_estate_districts_table',
                'create_real_estate_amenities_table',
                'create_real_estate_features_table',
                'create_real_estate_property_amenities_table',
                'create_real_estate_property_features_table',
                'update_properties_table_for_references',
                'make_properties_state_nullable',
                'make_properties_legacy_fields_nullable',
                'add_tenure_to_properties_table',
            ])
            ->hasCommands([
                InstallRealEstateCommand::class,
                VerifyRealEstateCommand::class,
            ])
            ->hasRoute('web');
    }

    public function packageRegistered(): void
    {
        // Bind the plugin as singleton
        $this->app->singleton(RealEstatePlugin::class, function ($app) {
            return new RealEstatePlugin();
        });
    }

    public function packageBooted(): void
    {
        // Register Livewire components
        $this->registerLivewireComponents();
        
        // Register Filament plugin (resources are attached via plugin)
        $this->registerFilamentPlugin();
        
        // Register blocks when the block manager is resolved
        $this->app->afterResolving(BlockManager::class, function (BlockManager $blockManager) {
            $this->registerPropertyBlocks($blockManager);
        });
    }


    /**
     * Register Livewire components for the Real Estate plugin
     */
    protected function registerLivewireComponents(): void
    {
        try {
            // Register Real Estate plugin Livewire components
            Livewire::component(
                'real-estate.property-search-component',
                PropertySearchComponent::class
            );
            
            logger()->debug('Real Estate Plugin: Registered Livewire components');
            
        } catch (\Exception $e) {
            logger()->error('Real Estate Plugin: Failed to register Livewire components', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Register the Filament plugin with the admin panel
     */
    protected function registerFilamentPlugin(): void
    {
        try {
            // Check if plugin tables exist before registering admin resources
            if (!$this->pluginTablesExist()) {
                logger()->info('Real Estate Plugin: Tables not found, skipping admin panel registration. Run "php artisan real-estate:install" first.');
                return;
            }

            $plugin = $this->makePluginFromConfig();
            $adminPanel = Filament::getPanel('admin');

            if (method_exists($adminPanel, 'plugins')) {
                $adminPanel->plugins([$plugin]);
                logger()->debug('Real Estate Plugin: Registered plugin on admin panel');
            } else {
                logger()->warning('Real Estate Plugin: Admin panel does not support plugins method; skipping plugin registration');
            }
            
        } catch (\Exception $e) {
            logger()->error('Real Estate Plugin: Failed to register Filament plugin', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Register Real Estate blocks with the block manager
     */
    protected function registerPropertyBlocks(BlockManager $blockManager): void
    {
        try {
            // Check if plugin tables exist before registering blocks
            if (!$this->pluginTablesExist()) {
                logger()->info('Real Estate Plugin: Tables not found, skipping block registration. Run "php artisan real-estate:install" first.');
                return;
            }

            $blocks = $this->enabledBlocks();

            foreach ($blocks as $blockClass) {
                if (!class_exists($blockClass) || !is_subclass_of($blockClass, AbstractBlock::class)) {
                    continue;
                }

                $blockManager->register($blockClass);
                logger()->debug("Real Estate Plugin: Registered block {$blockClass}");
            }
            
        } catch (\Exception $e) {
            logger()->error('Real Estate Plugin: Failed to register blocks', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if plugin tables exist
     */
    protected function pluginTablesExist(): bool
    {
        try {
            // Use the correct table name from our fixed implementation
            $propertiesTable = 'real_estate_properties';
            
            return \Schema::hasTable($propertiesTable);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Build plugin instance from configuration
     */
    protected function makePluginFromConfig(): RealEstatePlugin
    {
        $config = config('real-estate', []);
        $plugin = RealEstatePlugin::make();

        // Enable/disable sections based on config
        $plugin->propertyManagement(data_get($config, 'admin.enabled', true));
        $plugin->propertyBlocks($this->blocksEnabled());

        return $plugin;
    }

    /**
     * Determine enabled block classes from config
     */
    protected function enabledBlocks(): array
    {
        $blocks = data_get(config('real-estate'), 'blocks', []);

        return collect($blocks)
            ->filter(fn ($block) => data_get($block, 'enabled', true))
            ->pluck('class')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Check if any blocks are enabled
     */
    protected function blocksEnabled(): bool
    {
        return !empty($this->enabledBlocks());
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            RealEstatePlugin::class,
            InstallRealEstateCommand::class,
        ];
    }
}
