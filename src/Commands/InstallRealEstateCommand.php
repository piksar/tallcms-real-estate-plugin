<?php

namespace TallCms\RealEstate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallRealEstateCommand extends Command
{
    protected $signature = 'real-estate:install 
                           {--fresh : Run fresh migrations (warning: will drop existing data)}
                           {--seed : Run seeders after installation}
                           {--demo : Install with demo data (50 sample properties)}
                           {--publish : Publish config, views, and migrations for customization}';

    protected $description = 'Install the TALL CMS Real Estate plugin';

    public function handle()
    {
        $this->info('🏠 Installing TALL CMS Real Estate Plugin...');

        // Optional: publish assets/configs for customization
        if ($this->option('publish')) {
            $this->publishMigrations();
            $this->publishConfiguration();
            $this->publishViews();
        }

        // Step 1: Run migrations
        $this->runMigrations();
        
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

    private function runMigrations(): void
    {
        $this->info('🗃️  Running migrations...');
        
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️  This will drop all existing data. Continue?', false)) {
                Artisan::call('migrate:fresh');
            } else {
                $this->error('Installation cancelled');
                return;
            }
        } else {
            Artisan::call('migrate');
        }
        
        $this->line('   Database migrations completed');
    }

    private function seedReferenceData(): void
    {
        $this->info('🌱 Seeding reference data...');
        
        Artisan::call('db:seed', [
            '--class' => 'TallCms\\RealEstate\\Database\\Seeders\\RealEstateReferenceDataSeeder'
        ]);
        
        $this->line('   Reference data seeded (property types, districts, etc.)');
    }

    private function seedDemoData(): void
    {
        $this->info('🏘️  Installing demo properties...');
        
        Artisan::call('db:seed', [
            '--class' => 'TallCms\\RealEstate\\Database\\Seeders\\PropertySeeder'
        ]);
        
        $this->line('   50 demo properties installed');
    }

    private function registerWithTallCms(): void
    {
        $this->info('🔗 Registering with TALL CMS...');
        $this->line('   Plugin service provider will register blocks/resources automatically');
        $this->verifyBlockRegistration();
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
