<?php

namespace TallCms\RealEstate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_features';

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
     * Get the properties that have this feature
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'real_estate_property_features');
    }

    /**
     * Scope to get only active features
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get features by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get features ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get features for select options
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
     * Get available feature categories
     */
    public static function getCategories(): array
    {
        return [
            'interior' => 'Interior Features',
            'exterior' => 'Exterior Features',
            'kitchen' => 'Kitchen Features',
            'bathroom' => 'Bathroom Features',
            'flooring' => 'Flooring & Finishes',
            'utilities' => 'Utilities & Systems',
            'accessibility' => 'Accessibility Features',
        ];
    }

    /**
     * Get features grouped by category
     */
    public static function getByCategory(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('category')
            ->map(function ($features) {
                return $features->pluck('name', 'id')->toArray();
            })
            ->toArray();
    }
}