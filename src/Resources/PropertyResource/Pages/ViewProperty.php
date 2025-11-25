<?php

namespace TallCms\RealEstate\Resources\PropertyResource\Pages;

use TallCms\RealEstate\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}