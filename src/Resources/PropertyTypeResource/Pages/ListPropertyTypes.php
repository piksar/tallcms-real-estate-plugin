<?php

namespace TallCms\RealEstate\Resources\PropertyTypeResource\Pages;

use TallCms\RealEstate\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyTypes extends ListRecords
{
    protected static string $resource = PropertyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}