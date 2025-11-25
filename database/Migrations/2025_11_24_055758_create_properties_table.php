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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('property_type'); // house, apartment, condo, etc.
            $table->string('listing_status')->default('active'); // active, pending, sold, etc.
            
            // Location Information
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country')->default('US');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Property Details
            $table->integer('bedrooms')->nullable();
            $table->decimal('bathrooms', 3, 1)->nullable(); // 2.5 bathrooms
            $table->integer('half_bathrooms')->nullable();
            $table->integer('square_footage')->nullable();
            $table->decimal('lot_size', 8, 2)->nullable(); // acres or sq ft
            $table->integer('year_built')->nullable();
            $table->integer('garage_spaces')->nullable();
            
            // Features and Amenities
            $table->json('amenities')->nullable(); // pool, gym, etc.
            $table->json('features')->nullable(); // fireplace, hardwood floors, etc.
            
            // Media
            $table->json('photos')->nullable(); // array of photo URLs
            $table->string('virtual_tour_url')->nullable();
            $table->string('video_url')->nullable();
            
            // Agent/Contact Information
            $table->string('agent_name')->nullable();
            $table->string('agent_email')->nullable();
            $table->string('agent_phone')->nullable();
            
            // Listing Management
            $table->date('listing_date')->nullable();
            $table->date('available_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            
            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('seo_keywords')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['property_type', 'listing_status']);
            $table->index(['city', 'state']);
            $table->index(['price', 'property_type']);
            $table->index(['bedrooms', 'bathrooms']);
            $table->index(['is_published', 'is_featured']);
            $table->index(['listing_date', 'listing_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
