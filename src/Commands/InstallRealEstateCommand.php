<?php

namespace TallCms\RealEstate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallRealEstateCommand extends Command
{
    protected $signature = 'real-estate:install 
                           {--fresh : Reset ONLY plugin tables and re-install (safe: preserves users and other data)}
                           {--seed : Run seeders after installation}
                           {--demo : Install with demo data (50 sample properties)}
                           {--publish : Publish config, views, and migrations for customization}';

    protected $description = 'Install the TALL CMS Real Estate plugin';

    public function handle(): int
    {
        $this->info('🏠 Installing TALL CMS Real Estate Plugin...');

        // Optional: publish assets/configs for customization
        if ($this->option('publish')) {
            $this->publishMigrations();
            $this->publishConfiguration();
            $this->publishViews();
        }

        // Step 1: Run migrations
        if (!$this->runMigrations()) {
            return self::FAILURE;
        }
        
        // Step 2: Seed reference data
        $this->seedReferenceData();
        
        // Step 3: Optional demo data
        if ($this->option('demo')) {
            $this->seedDemoData();
        }
        
        // Step 4: Register with TALL CMS (handled via service provider hooks)
        $this->registerWithTallCms();

        $this->info('✅ Real Estate plugin installed successfully!');
        $this->displayPostInstallInstructions();
        
        return self::SUCCESS;
    }

    private function publishMigrations(): void
    {
        $this->info('📦 Publishing migrations...');
        
        Artisan::call('vendor:publish', [
            '--provider' => 'TallCms\\RealEstate\\RealEstateServiceProvider',
            '--tag' => 'real-estate-migrations'
        ]);
        
        $this->line('   Migrations published');
    }

    private function publishConfiguration(): void
    {
        $this->info('⚙️  Publishing configuration...');
        
        Artisan::call('vendor:publish', [
            '--provider' => 'TallCms\\RealEstate\\RealEstateServiceProvider',
            '--tag' => 'real-estate-config'
        ]);
        
        $this->line('   Configuration published');
    }

    private function publishViews(): void
    {
        $this->info('🎨 Publishing views...');
        
        Artisan::call('vendor:publish', [
            '--provider' => 'TallCms\\RealEstate\\RealEstateServiceProvider',
            '--tag' => 'real-estate-views'
        ]);
        
        $this->line('   Views published to resources/views/vendor/real-estate');
    }

    private function runMigrations(): bool
    {
        $this->info('🗃️  Running migrations...');
        
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️  This will reset ONLY the Real Estate plugin tables and data. Continue?', false)) {
                $this->freshInstallPluginMigrations();
            } else {
                $this->error('Installation cancelled - no changes made');
                return false;
            }
        } else {
            // First run main migrations
            Artisan::call('migrate');
            
            // Then run plugin-specific migrations from vendor path
            $migrationPath = __DIR__ . '/../../database/Migrations';
            if (is_dir($migrationPath)) {
                Artisan::call('migrate', [
                    '--path' => str_replace(base_path() . '/', '', $migrationPath)
                ]);
            }
        }
        
        $this->line('   Database migrations completed');
        return true;
    }

    /**
     * Safely reset only the Real Estate plugin tables and data
     */
    private function freshInstallPluginMigrations(): void
    {
        $this->info('🧹 Resetting Real Estate plugin tables...');
        
        $pluginTables = $this->pluginTables();
        
        try {
            Schema::disableForeignKeyConstraints();
            
            foreach ($pluginTables as $table) {
                if (Schema::hasTable($table)) {
                    Schema::dropIfExists($table);
                    $this->line("   Dropped table: {$table}");
                }
            }
            
            // Remove plugin migration records
            $migrationRecords = $this->pluginMigrationNames();
            
            DB::table('migrations')->whereIn('migration', $migrationRecords)->delete();
            $this->line('   Removed plugin migration records');
            
            // Re-run plugin migrations
            $migrationPath = __DIR__ . '/../../database/Migrations';
            if (is_dir($migrationPath)) {
                Artisan::call('migrate', [
                    '--path' => str_replace(base_path() . '/', '', $migrationPath)
                ]);
                $this->line('   Re-ran plugin migrations');
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to reset plugin tables: ' . $e->getMessage());
            throw $e;
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    private function seedReferenceData(): void
    {
        $this->info('🌱 Seeding reference data...');
        
        $class = \TallCms\RealEstate\Database\Seeders\RealEstateReferenceDataSeeder::class;

        try {
            $this->call('db:seed', ['--class' => $class]);
            $this->line('   Reference data seeded (property types, districts, etc.)');
        } catch (\Exception $e) {
            $this->error('Failed to seed reference data: ' . $e->getMessage());
        }
    }

    private function seedDemoData(): void
    {
        $this->info('🏘️  Installing demo properties...');
        
        $class = \TallCms\RealEstate\Database\Seeders\PropertySeeder::class;

        try {
            $this->call('db:seed', ['--class' => $class]);
            $this->line('   50 demo properties installed');
        } catch (\Exception $e) {
            $this->error('Failed to seed demo data: ' . $e->getMessage());
        }
    }

    private function registerWithTallCms(): void
    {
        $this->info('🔗 Registering with TALL CMS...');
        $this->line('   Plugin service provider will register blocks/resources automatically');
        $this->verifyBlockRegistration();
    }

    /**
     * Build table list from config to avoid hard-coding names
     */
    private function pluginTables(): array
    {
        $dbConfig = config('real-estate.database', []);
        $prefix = $dbConfig['table_prefix'] ?? 'real_estate_';
        $propertiesTable = $dbConfig['properties_table'] ?? 'properties';

        return [
            $propertiesTable,
            "{$prefix}property_types",
            "{$prefix}districts",
            "{$prefix}amenities",
            "{$prefix}features",
            "{$prefix}property_amenities",
            "{$prefix}property_features",
        ];
    }

    /**
     * Build migration name list from plugin migration files
     */
    private function pluginMigrationNames(): array
    {
        $migrationPath = __DIR__ . '/../../database/Migrations';
        if (!is_dir($migrationPath)) {
            return [];
        }

        return collect(scandir($migrationPath))
            ->filter(fn ($file) => Str::endsWith($file, '.php'))
            ->map(fn ($file) => pathinfo($file, PATHINFO_FILENAME))
            ->values()
            ->all();
    }

    private function verifyBlockRegistration(): void
    {
        $this->info('🔍 Verifying block registration...');
        
        $blocks = [
            'PropertySearchBlock' => \TallCms\RealEstate\Blocks\PropertySearchBlock::class,
            'PropertyListingsBlock' => \TallCms\RealEstate\Blocks\PropertyListingsBlock::class,
            'FeaturedPropertyBlock' => \TallCms\RealEstate\Blocks\FeaturedPropertyBlock::class,
        ];
        
        foreach ($blocks as $name => $class) {
            if (class_exists($class)) {
                $this->line("   ✅ {$name} class found");
            } else {
                $this->error("   ❌ {$name} class not found");
            }
        }
    }

    private function displayPostInstallInstructions(): void
    {
        $this->info('');
        $this->info('🎉 Installation Complete!');
        $this->info('');
        $this->info('Next Steps:');
        $this->line('1. Visit /admin to see the new Real Estate resources');
        $this->line('2. Add Property Search blocks to your pages via the page builder');
        $this->line('3. Configure the plugin in config/real-estate.php if needed');
        $this->line('4. Visit the Block Library to see available Real Estate blocks');
        $this->info('');
        $this->info('Documentation: https://docs.tallcms.com/plugins/real-estate');
    }
}
