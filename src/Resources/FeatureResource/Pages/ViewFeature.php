<?php

namespace TallCms\RealEstate\Resources\FeatureResource\Pages;

use TallCms\RealEstate\Resources\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeature extends ViewRecord
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}