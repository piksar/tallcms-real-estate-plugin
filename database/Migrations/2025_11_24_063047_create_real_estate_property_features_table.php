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
        Schema::create('real_estate_property_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('real_estate_properties')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('real_estate_features')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['property_id', 'feature_id']);
            $table->index('property_id');
            $table->index('feature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_property_features');
    }
};