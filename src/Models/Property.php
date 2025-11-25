<?php

namespace TallCms\RealEstate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_properties';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'property_type', // Keep for backward compatibility
        'property_type_id', // New foreign key
        'tenure', // Property tenure type (Freehold, 99-year, etc.)
        'listing_status',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'district_id', // New foreign key
        'latitude',
        'longitude',
        'bedrooms',
        'bathrooms',
        'half_bathrooms',
        'square_footage',
        'lot_size',
        'year_built',
        'garage_spaces',
        'amenities', // Keep for backward compatibility (will be phased out)
        'features', // Keep for backward compatibility (will be phased out)
        'photos',
        'virtual_tour_url',
        'video_url',
        'agent_name',
        'agent_email',
        'agent_phone',
        'listing_date',
        'available_date',
        'is_featured',
        'is_published',
        'meta_title',
        'meta_description',
        'seo_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'square_footage' => 'integer',
        'lot_size' => 'decimal:2',
        'year_built' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'decimal:1',
        'half_bathrooms' => 'integer',
        'garage_spaces' => 'integer',
        'amenities' => 'array',
        'features' => 'array',
        'photos' => 'array',
        'seo_keywords' => 'array',
        'listing_date' => 'date',
        'available_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected $dates = [
        'listing_date',
        'available_date',
        'deleted_at',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
            
            if (empty($property->listing_date)) {
                $property->listing_date = now();
            }
        });

        static::updating(function ($property) {
            if ($property->isDirty('title') && empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });
    }

    /**
     * Scope for published properties
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for active listings
     */
    public function scopeActive($query)
    {
        return $query->where('listing_status', 'active');
    }

    /**
     * Get the property type that owns this property
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the district that owns this property
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the amenities for this property
     */
    public function propertyAmenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'real_estate_property_amenities', 'property_id', 'amenity_id');
    }

    /**
     * Get the features for this property
     */
    public function propertyFeatures(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'real_estate_property_features', 'property_id', 'feature_id');
    }

    /**
     * Scope for properties by type
     */
    public function scopeOfType($query, $type)
    {
        // Support both new relationship and old field for backward compatibility
        if (is_numeric($type)) {
            return $query->where('property_type_id', $type);
        }
        
        // If it's a string, try to find the property type by slug first
        $propertyType = PropertyType::where('slug', $type)->first();
        if ($propertyType) {
            return $query->where('property_type_id', $propertyType->id);
        }
        
        // Fallback to old field for legacy data
        return $query->where('property_type', $type);
    }

    /**
     * Scope for price range filtering
     */
    public function scopePriceBetween($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope for location search
     */
    public function scopeInLocation($query, $location)
    {
        return $query->where(function ($query) use ($location) {
            $query->where('city', 'like', "%{$location}%")
                  ->orWhere('state', 'like', "%{$location}%")
                  ->orWhere('address', 'like', "%{$location}%")
                  ->orWhere('zip_code', 'like', "%{$location}%");
        });
    }

    /**
     * Scope for bedroom count
     */
    public function scopeWithBedrooms($query, $bedrooms)
    {
        if ($bedrooms === '5+') {
            return $query->where('bedrooms', '>=', 5);
        }
        return $query->where('bedrooms', $bedrooms);
    }

    /**
     * Scope for bathroom count
     */
    public function scopeWithBathrooms($query, $bathrooms)
    {
        if ($bathrooms === '4+') {
            return $query->where('bathrooms', '>=', 4);
        }
        return $query->where('bathrooms', $bathrooms);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'C$',
        ];

        $symbol = $currencySymbols[$this->currency] ?? '$';
        
        return $symbol . number_format($this->price, 0);
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get property type label
     */
    public function getPropertyTypeLabel(): string
    {
        $plugin = \TallCms\RealEstate\RealEstatePlugin::get();
        $types = $plugin->getPropertyTypes();
        
        return $types[$this->property_type] ?? ucfirst($this->property_type);
    }

    /**
     * Get listing status label
     */
    public function getListingStatusLabel(): string
    {
        $plugin = \TallCms\RealEstate\RealEstatePlugin::get();
        $statuses = $plugin->getListingStatuses();
        
        return $statuses[$this->listing_status] ?? ucfirst($this->listing_status);
    }

    /**
     * Get primary photo
     */
    public function getPrimaryPhotoAttribute(): ?string
    {
        return $this->photos[0] ?? null;
    }

    /**
     * Get primary image (alias for primary_photo)
     */
    public function getPrimaryImageAttribute(): ?string
    {
        return $this->primary_photo;
    }

    /**
     * Get property URL
     */
    public function getUrlAttribute(): string
    {
        return route('property.show', $this->slug);
    }

    /**
     * Get bathroom display text
     */
    public function getBathroomDisplayAttribute(): string
    {
        $total = $this->bathrooms + ($this->half_bathrooms * 0.5);
        return number_format($total, ($total == intval($total)) ? 0 : 1);
    }

    /**
     * Check if property has virtual tour
     */
    public function hasVirtualTour(): bool
    {
        return !empty($this->virtual_tour_url);
    }

    /**
     * Check if property has video
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    /**
     * Get SEO meta title
     */
    protected function metaTitle(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: "{$this->title} | {$this->getPropertyTypeLabel()} for Sale in {$this->city}",
        );
    }

    /**
     * Get SEO meta description
     */
    protected function metaDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: Str::limit(strip_tags($this->description), 160),
        );
    }

    /**
     * Generate structured data for SEO
     */
    public function getStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $this->title,
            'description' => strip_tags($this->description),
            'url' => $this->url,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->address,
                'addressLocality' => $this->city,
                'addressRegion' => $this->state,
                'postalCode' => $this->zip_code,
                'addressCountry' => $this->country,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $this->price,
                'priceCurrency' => $this->currency,
                'availability' => $this->listing_status === 'active' ? 'InStock' : 'OutOfStock',
            ],
            'floorSize' => [
                '@type' => 'QuantitativeValue',
                'value' => $this->square_footage,
                'unitText' => 'sq ft',
            ],
            'numberOfRooms' => $this->bedrooms,
            'numberOfBathroomsTotal' => $this->bathroom_display,
        ];
    }
}