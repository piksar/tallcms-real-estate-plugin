<?php

namespace TallCms\RealEstate\Resources\PropertyTypeResource\Pages;

use TallCms\RealEstate\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyType extends EditRecord
{
    protected static string $resource = PropertyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}