<?php

namespace TallCms\RealEstate\Commands;

use Illuminate\Console\Command;
use App\TallCms\PageBuilder\BlockManager;

class VerifyRealEstateCommand extends Command
{
    protected $signature = 'real-estate:verify';

    protected $description = 'Verify Real Estate plugin installation and block registration';

    public function handle()
    {
        $this->info('🔍 Verifying TALL CMS Real Estate Plugin...');

        // Check plugin classes
        $this->verifyPluginClasses();
        
        // Check block registration
        $this->verifyBlockRegistration();
        
        // Check database tables
        $this->verifyDatabaseTables();
        
        // Check Filament resources
        $this->verifyFilamentResources();

        $this->info('✅ Verification complete!');
    }

    private function verifyPluginClasses(): void
    {
        $this->info('📦 Checking plugin classes...');
        
        $classes = [
            'Service Provider' => \TallCms\RealEstate\RealEstateServiceProvider::class,
            'Property Model' => \TallCms\RealEstate\Models\Property::class,
            'Property Resource' => \TallCms\RealEstate\Resources\PropertyResource::class,
            'Search Component' => \TallCms\RealEstate\Livewire\PropertySearchComponent::class,
        ];
        
        foreach ($classes as $name => $class) {
            if (class_exists($class)) {
                $this->line("   ✅ {$name}");
            } else {
                $this->error("   ❌ {$name} - Class not found: {$class}");
            }
        }
    }

    private function verifyBlockRegistration(): void
    {
        $this->info('🧩 Checking block registration...');
        
        try {
            $blockManager = app(BlockManager::class);
            $registeredBlocks = $blockManager->getRegisteredBlocks();
            
            $expectedBlocks = [
                'property-search-block' => \TallCms\RealEstate\Blocks\PropertySearchBlock::class,
                'property-listings-block' => \TallCms\RealEstate\Blocks\PropertyListingsBlock::class,
                'featured-property-block' => \TallCms\RealEstate\Blocks\FeaturedPropertyBlock::class,
            ];
            
            foreach ($expectedBlocks as $blockName => $blockClass) {
                if (isset($registeredBlocks[$blockName])) {
                    $this->line("   ✅ {$blockName}");
                } else {
                    $this->error("   ❌ {$blockName} - Not registered");
                    
                    // Try to register it manually
                    if (class_exists($blockClass)) {
                        try {
                            $blockManager->register($blockClass);
                            $this->line("   ℹ️  {$blockName} - Registered manually");
                        } catch (\Exception $e) {
                            $this->error("   ❌ {$blockName} - Failed to register: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // Show all registered blocks for debugging
            $this->line('');
            $this->info('📋 All registered blocks:');
            foreach ($registeredBlocks as $name => $class) {
                $source = str_contains($class, 'TallCms\\RealEstate') ? '[PLUGIN]' : '[CORE]';
                $this->line("   - {$name} {$source}");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ BlockManager error: " . $e->getMessage());
        }
    }

    private function verifyDatabaseTables(): void
    {
        $this->info('🗃️  Checking database tables...');
        
        $prefix = config('real-estate.database.table_prefix', 'real_estate_');
        $tables = [
            config('real-estate.database.properties_table', 'properties'),
            $prefix . 'property_types',
            $prefix . 'districts',
            $prefix . 'amenities',
            $prefix . 'features',
        ];
        
        foreach ($tables as $table) {
            try {
                \DB::table($table)->limit(1)->count();
                $this->line("   ✅ {$table}");
            } catch (\Exception $e) {
                $this->error("   ❌ {$table} - Table not found or accessible");
            }
        }
    }

    private function verifyFilamentResources(): void
    {
        $this->info('⚙️  Checking Filament resources...');
        
        $resources = [
            'PropertyResource' => \TallCms\RealEstate\Resources\PropertyResource::class,
            'PropertyTypeResource' => \TallCms\RealEstate\Resources\PropertyTypeResource::class,
            'DistrictResource' => \TallCms\RealEstate\Resources\DistrictResource::class,
        ];
        
        foreach ($resources as $name => $class) {
            if (class_exists($class)) {
                try {
                    // Try to instantiate the resource
                    new $class();
                    $this->line("   ✅ {$name}");
                } catch (\Exception $e) {
                    $this->error("   ❌ {$name} - Error: " . $e->getMessage());
                }
            } else {
                $this->error("   ❌ {$name} - Class not found");
            }
        }
    }
}
