<?php

namespace TallCms\RealEstate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_amenities';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the properties that have this amenity
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'real_estate_property_amenities');
    }

    /**
     * Scope to get only active amenities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get amenities by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get amenities ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get amenities for select options
     */
    public static function getSelectOptions(string $category = null): array
    {
        $query = static::active()->ordered();
        
        if ($category) {
            $query->byCategory($category);
        }
        
        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get available amenity categories
     */
    public static function getCategories(): array
    {
        return [
            'indoor' => 'Indoor Features',
            'outdoor' => 'Outdoor Features', 
            'building' => 'Building Amenities',
            'location' => 'Location Benefits',
            'security' => 'Security Features',
            'parking' => 'Parking & Transportation',
        ];
    }

    /**
     * Get amenities grouped by category
     */
    public static function getByCategory(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('category')
            ->map(function ($amenities) {
                return $amenities->pluck('name', 'id')->toArray();
            })
            ->toArray();
    }
}