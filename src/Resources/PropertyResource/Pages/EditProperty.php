<?php

namespace TallCms\RealEstate\Resources\PropertyResource\Pages;

use TallCms\RealEstate\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract relationship data
        $amenities = $data['propertyAmenities'] ?? [];
        $features = $data['propertyFeatures'] ?? [];
        
        // Handle legacy amenities and features (ensure they're arrays for JSON storage)
        if (isset($data['amenities']) && !is_array($data['amenities'])) {
            $data['amenities'] = [];
        }
        if (isset($data['features']) && !is_array($data['features'])) {
            $data['features'] = [];
        }
        if (isset($data['photos']) && !is_array($data['photos'])) {
            $data['photos'] = [];
        }
        if (isset($data['seo_keywords']) && !is_array($data['seo_keywords'])) {
            $data['seo_keywords'] = [];
        }
        
        // Handle legacy property_type field for backward compatibility
        if (isset($data['property_type_id']) && !empty($data['property_type_id'])) {
            $propertyType = \TallCms\RealEstate\Models\PropertyType::find($data['property_type_id']);
            if ($propertyType) {
                $data['property_type'] = $propertyType->slug; // Store slug for backward compatibility
            }
        }
        
        // Remove relationship fields from main data to prevent array to string conversion
        unset($data['propertyAmenities'], $data['propertyFeatures']);

        // Update the property record
        $record->update($data);

        // Sync relationships (this will add/remove as needed)
        $record->propertyAmenities()->sync($amenities);
        $record->propertyFeatures()->sync($features);

        return $record;
    }
}