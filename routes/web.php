<?php

use TallCms\RealEstate\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

// Property detail route (keep this for individual property pages)
Route::get('/property/{slug}', [PropertyController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('property.show');

// Note: /properties route removed - now handled by CMS page with PropertySearchBlock