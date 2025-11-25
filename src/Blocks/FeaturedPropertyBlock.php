<?php

namespace TallCms\RealEstate\Blocks;

use App\TallCms\PageBuilder\AbstractBlock;
use TallCms\RealEstate\Models\Property;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class FeaturedPropertyBlock extends AbstractBlock
{
    public function getDisplayName(): string
    {
        return 'Featured Property Spotlight';
    }

    public function getDescription(): string
    {
        return 'Showcase a single property with detailed information and large imagery';
    }

    public function getCategory(): string
    {
        return 'Real Estate';
    }

    public function getIcon(): string
    {
        return 'heroicon-o-star';
    }

    public function getDefaults(): array
    {
        return [
            'title' => 'Property of the Month',
            'subtitle' => 'Discover this exceptional property',
            'property_selection' => 'automatic', // automatic, manual
            'property_id' => null,
            'show_price' => true,
            'show_details' => true,
            'show_amenities' => true,
            'show_agent_info' => true,
            'show_cta_button' => true,
            'cta_text' => 'View Details',
            'layout_style' => 'side_by_side', // side_by_side, stacked, overlay
        ];
    }

    public function getSchema(): array
    {
        return [
            Section::make('Content')
                ->schema([
                    TextInput::make('title')
                        ->label('Section Title'),
                    
                    Textarea::make('subtitle')
                        ->label('Section Subtitle')
                        ->rows(2),
                ])
                ->collapsible(),

            Section::make('Property Selection')
                ->schema([
                    Select::make('property_selection')
                        ->label('How to Select Property')
                        ->options([
                            'automatic' => 'Automatically show latest featured property',
                            'manual' => 'Manually select specific property',
                        ])
                        ->default('automatic')
                        ->live(),
                    
                    Select::make('property_id')
                        ->label('Select Property')
                        ->searchable()
                        ->options(function () {
                            return Property::published()
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->toArray();
                        })
                        ->visible(fn ($get) => $get('property_selection') === 'manual')
                        ->helperText('Choose which specific property to feature'),
                ])
                ->collapsible(),

            Section::make('Display Options')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('layout_style')
                                ->label('Layout Style')
                                ->options([
                                    'side_by_side' => 'Side by Side',
                                    'stacked' => 'Stacked',
                                    'overlay' => 'Image with Overlay',
                                ])
                                ->default('side_by_side'),
                            
                            Toggle::make('show_cta_button')
                                ->label('Show Call-to-Action Button')
                                ->default(true)
                                ->live(),
                        ]),
                    
                    TextInput::make('cta_text')
                        ->label('Button Text')
                        ->default('View Details')
                        ->visible(fn ($get) => $get('show_cta_button')),
                ])
                ->collapsible(),

            Section::make('Property Information')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Toggle::make('show_price')
                                ->label('Show Price')
                                ->default(true),
                            
                            Toggle::make('show_details')
                                ->label('Show Property Details')
                                ->default(true)
                                ->helperText('Bedrooms, bathrooms, square footage'),
                        ]),
                    
                    Grid::make(2)
                        ->schema([
                            Toggle::make('show_amenities')
                                ->label('Show Amenities')
                                ->default(true),
                            
                            Toggle::make('show_agent_info')
                                ->label('Show Agent Information')
                                ->default(true),
                        ]),
                ])
                ->collapsible(),

            // Include built-in spacing and animation settings
            self::getSpacingFormSchema(),
            self::getAnimationFormSchema(),
        ];
    }

    public function processData(array $data): array
    {
        $property = null;

        if ($data['property_selection'] === 'manual' && $data['property_id']) {
            $property = Property::published()->find($data['property_id']);
        } else {
            // Automatic selection - get latest featured property
            $property = Property::published()->featured()->latest('listing_date')->first();
            
            // If no featured property, get latest published property
            if (!$property) {
                $property = Property::published()->latest('listing_date')->first();
            }
        }

        $data['property'] = $property;
        $data['has_property'] = $property !== null;

        return $data;
    }
}