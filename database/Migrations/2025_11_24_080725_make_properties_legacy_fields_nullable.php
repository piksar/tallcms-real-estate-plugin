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
            // Make legacy property_type field nullable since we're using property_type_id now
            $table->string('property_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table) {
            $table->string('property_type')->nullable(false)->change();
        });
    }
};