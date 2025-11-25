<?php

namespace TallCms\RealEstate\Resources\AmenityResource\Pages;

use TallCms\RealEstate\Resources\AmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAmenity extends ViewRecord
{
    protected static string $resource = AmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}