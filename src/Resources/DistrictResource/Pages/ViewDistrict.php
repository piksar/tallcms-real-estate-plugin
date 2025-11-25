<?php

namespace TallCms\RealEstate\Resources\DistrictResource\Pages;

use TallCms\RealEstate\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDistrict extends ViewRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}