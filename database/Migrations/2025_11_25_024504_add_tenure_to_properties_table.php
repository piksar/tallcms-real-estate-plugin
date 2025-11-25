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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('tenure')->nullable()->after('property_type_id')
                  ->comment('Property tenure type (Freehold, 99-year, 999-year, etc.)');
            $table->index('tenure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['tenure']);
            $table->dropColumn('tenure');
        });
    }
};
