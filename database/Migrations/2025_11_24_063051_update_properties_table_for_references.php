<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table) {
            // Add foreign key references
            $table->foreignId('property_type_id')
                  ->nullable()
                  ->after('property_type')
                  ->constrained('real_estate_property_types')
                  ->nullOnDelete();
                  
            $table->foreignId('district_id')
                  ->nullable()
                  ->after('city')
                  ->constrained('real_estate_districts')
                  ->nullOnDelete();
            
            // Keep old fields for migration compatibility, remove them later
            // We'll create a separate migration to drop these once data is migrated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('property_type_id');
            $table->dropConstrainedForeignId('district_id');
        });
    }
};