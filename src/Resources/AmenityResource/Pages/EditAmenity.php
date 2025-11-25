<?php

namespace TallCms\RealEstate\Resources\AmenityResource\Pages;

use TallCms\RealEstate\Resources\AmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmenity extends EditRecord
{
    protected static string $resource = AmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}