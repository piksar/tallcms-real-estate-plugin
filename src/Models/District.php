<?php

namespace TallCms\RealEstate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_districts';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'country',
        'state_province',
        'postal_code_prefix',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the properties in this district
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Scope to get only active districts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get districts by country
     */
    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to get districts ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get districts for select options
     */
    public static function getSelectOptions(string $country = null): array
    {
        $query = static::active()->ordered();
        
        if ($country) {
            $query->byCountry($country);
        }
        
        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get full display name with state/province
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->name;
        
        if ($this->state_province) {
            $name .= ', ' . $this->state_province;
        }
        
        return $name;
    }
}