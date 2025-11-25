<?php

namespace TallCms\RealEstate\Resources\PropertyTypeResource\Pages;

use TallCms\RealEstate\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPropertyType extends ViewRecord
{
    protected static string $resource = PropertyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}