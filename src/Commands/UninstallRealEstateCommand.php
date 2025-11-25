<?php

namespace TallCms\RealEstate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class UninstallRealEstateCommand extends Command
{
    protected $signature = 'real-estate:uninstall 
                           {--remove-data : Remove all plugin tables and data (DESTRUCTIVE)}
                           {--force : Skip confirmation prompts (for automation)}
                           {--keep-migrations : Keep migration records in migrations table}';

    protected $description = 'Uninstall the TALL CMS Real Estate plugin';

    /**
     * Plugin tables to manage during uninstall
     */
    protected array $pluginTables = [
        'real_estate_properties',
        'real_estate_property_types', 
        'real_estate_districts',
        'real_estate_amenities',
        'real_estate_features',
        'real_estate_property_amenities',
        'real_estate_property_features',
    ];

    /**
     * Migration files to remove from migrations table
     */
    protected array $pluginMigrations = [
        '2025_11_24_055758_create_properties_table',
        '2025_11_24_063030_create_real_estate_property_types_table',
        '2025_11_24_063034_create_real_estate_districts_table',
        '2025_11_24_063037_create_real_estate_amenities_table',
        '2025_11_24_063040_create_real_estate_features_table',
        '2025_11_24_063044_create_real_estate_property_amenities_table',
        '2025_11_24_063047_create_real_estate_property_features_table',
        '2025_11_24_063051_update_properties_table_for_references',
        '2025_11_24_080514_make_properties_state_nullable',
        '2025_11_24_080725_make_properties_legacy_fields_nullable',
        '2025_11_25_024504_add_tenure_to_properties_table',
    ];

    public function handle(): int
    {
        $this->info('🏠 TALL CMS Real Estate Plugin Uninstall');
        $this->newLine();

        // Check what exists
        $existingTables = $this->getExistingPluginTables();
        $dataCount = $this->getDataSummary($existingTables);

        if (empty($existingTables)) {
            $this->info('✅ No plugin tables found. Plugin appears to be already uninstalled.');
            return self::SUCCESS;
        }

        // Display current state
        $this->displayCurrentState($existingTables, $dataCount);
        
        // Determine uninstall type
        if ($this->option('remove-data')) {
            return $this->performFullUninstall($existingTables, $dataCount);
        } else {
            return $this->performSafeUninstall();
        }
    }

    /**
     * Display current plugin state
     */
    protected function displayCurrentState(array $existingTables, array $dataCount): void
    {
        $this->info('📊 Current Plugin State:');
        
        foreach ($existingTables as $table) {
            $count = $dataCount[$table] ?? 0;
            $this->line("   • {$table}: {$count} records");
        }
        $this->newLine();
    }

    /**
     * Perform safe uninstall (code only)
     */
    protected function performSafeUninstall(): int
    {
        $this->info('🛡️  SAFE UNINSTALL MODE (Default)');
        $this->info('   • Plugin code will be removed via composer');
        $this->info('   • All data and tables will be preserved');
        $this->info('   • You can reinstall the plugin later without data loss');
        $this->newLine();

        $this->info('To complete the safe uninstall, run:');
        $this->line('   composer remove tallcms/real-estate-plugin');
        $this->newLine();

        $this->info('To perform a complete uninstall with data removal:');
        $this->line('   php artisan real-estate:uninstall --remove-data');

        return self::SUCCESS;
    }

    /**
     * Perform full uninstall (code + data)
     */
    protected function performFullUninstall(array $existingTables, array $dataCount): int
    {
        $this->error('⚠️  DESTRUCTIVE UNINSTALL MODE');
        $this->error('   This will permanently delete all plugin data!');
        $this->newLine();

        // Show what will be lost
        $totalRecords = array_sum($dataCount);
        if ($totalRecords > 0) {
            $this->error("📉 Data that will be PERMANENTLY LOST:");
            foreach ($dataCount as $table => $count) {
                if ($count > 0) {
                    $this->line("   • {$table}: {$count} records");
                }
            }
            $this->newLine();
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you absolutely sure you want to delete ALL plugin data?')) {
                $this->info('Uninstall cancelled.');
                return self::SUCCESS;
            }

            if ($totalRecords > 0) {
                $confirmText = 'DELETE ALL DATA';
                $input = $this->ask("Type '{$confirmText}' to confirm data deletion:");
                
                if ($input !== $confirmText) {
                    $this->info('Confirmation failed. Uninstall cancelled.');
                    return self::SUCCESS;
                }
            }
        }

        // Perform the uninstall
        $this->info('🗑️  Removing plugin data...');
        
        try {
            $this->dropPluginTables($existingTables);
            $this->cleanMigrationRecords();
            
            $this->info('✅ Plugin data removed successfully.');
            $this->info('To complete the uninstall, run:');
            $this->line('   composer remove tallcms/real-estate-plugin');
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to uninstall plugin: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Get existing plugin tables
     */
    protected function getExistingPluginTables(): array
    {
        return array_filter($this->pluginTables, function ($table) {
            return Schema::hasTable($table);
        });
    }

    /**
     * Get data summary for each table
     */
    protected function getDataSummary(array $tables): array
    {
        $summary = [];
        
        foreach ($tables as $table) {
            try {
                $summary[$table] = DB::table($table)->count();
            } catch (\Exception $e) {
                $summary[$table] = 0;
            }
        }
        
        return $summary;
    }

    /**
     * Drop plugin tables in correct order (respecting foreign keys)
     */
    protected function dropPluginTables(array $existingTables): void
    {
        // Drop in reverse dependency order
        $dropOrder = [
            'real_estate_property_amenities',
            'real_estate_property_features', 
            'real_estate_properties',
            'real_estate_property_types',
            'real_estate_districts',
            'real_estate_amenities',
            'real_estate_features',
        ];

        foreach ($dropOrder as $table) {
            if (in_array($table, $existingTables)) {
                $this->line("   Dropping table: {$table}");
                Schema::dropIfExists($table);
            }
        }
    }

    /**
     * Clean migration records
     */
    protected function cleanMigrationRecords(): void
    {
        if ($this->option('keep-migrations')) {
            $this->info('   Keeping migration records as requested.');
            return;
        }

        $this->line('   Cleaning migration records...');
        
        try {
            DB::table('migrations')
                ->whereIn('migration', $this->pluginMigrations)
                ->delete();
                
            $this->line('   Migration records cleaned.');
        } catch (\Exception $e) {
            $this->warn('   Could not clean migration records: ' . $e->getMessage());
        }
    }
}