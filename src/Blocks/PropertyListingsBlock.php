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

class PropertyListingsBlock extends AbstractBlock
{
    public function getDisplayName(): string
    {
        return 'Property Listings';
    }

    public function getDescription(): string
    {
        return 'Display a curated list of properties with filtering options';
    }

    public function getCategory(): string
    {
        return 'Real Estate';
    }

    public function getIcon(): string
    {
        return 'heroicon-o-building-office-2';
    }

    public function getDefaults(): array
    {
        return [
            'title' => 'Featured Properties',
            'subtitle' => 'Discover our hand-picked selection of premium properties',
            'display_type' => 'featured', // featured, latest, specific_type
            'property_type' => null,
            'limit' => 6,
            'layout' => 'grid',
            'show_filters' => false,
            'show_view_all_button' => true,
            'view_all_text' => 'View All Properties',
            'view_all_url' => '/properties',
        ];
    }

    public function getSchema(): array
    {
        $plugin = app(\TallCms\RealEstate\RealEstatePlugin::class);
        
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

            Section::make('Display Settings')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('display_type')
                                ->label('Display Type')
                                ->options([
                                    'featured' => 'Featured Properties',
                                    'latest' => 'Latest Properties',
                                    'specific_type' => 'Specific Property Type',
                                    'price_range' => 'Price Range',
                                ])
                                ->default('featured')
                                ->live(),
                            
                            Select::make('property_type')
                                ->label('Property Type')
                                ->options($plugin->getPropertyTypes())
                                ->visible(fn ($get) => $get('display_type') === 'specific_type'),
                        ]),
                    
                    Grid::make(3)
                        ->schema([
                            Select::make('limit')
                                ->label('Number to Show')
                                ->options([
                                    3 => '3 properties',
                                    6 => '6 properties',
                                    9 => '9 properties',
                                    12 => '12 properties',
                                ])
                                ->default(6),
                            
                            Select::make('layout')
                                ->label('Layout Style')
                                ->options([
                                    'grid' => 'Grid Layout',
                                    'list' => 'List Layout',
                                    'carousel' => 'Carousel/Slider',
                                ])
                                ->default('grid'),
                            
                            Toggle::make('show_filters')
                                ->label('Show Sort Options')
                                ->helperText('Allow users to sort the displayed properties'),
                        ]),
                ])
                ->collapsible(),

            Section::make('View All Button')
                ->schema([
                    Toggle::make('show_view_all_button')
                        ->label('Show "View All" Button')
                        ->default(true)
                        ->live(),
                    
                    Grid::make(2)
                        ->schema([
                            TextInput::make('view_all_text')
                                ->label('Button Text')
                                ->default('View All Properties')
                                ->visible(fn ($get) => $get('show_view_all_button')),
                            
                            TextInput::make('view_all_url')
                                ->label('Button URL')
                                ->default('/properties')
                                ->visible(fn ($get) => $get('show_view_all_button')),
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
        $query = Property::published();

        // Apply display type filtering
        switch ($data['display_type']) {
            case 'featured':
                $query->featured();
                break;
            case 'latest':
                $query->orderBy('listing_date', 'desc');
                break;
            case 'specific_type':
                if ($data['property_type']) {
                    $query->ofType($data['property_type']);
                }
                break;
            case 'price_range':
                // Could add price range filtering here
                break;
        }

        // Apply user sorting if filters are shown
        $sort = request('sort');
        if ($data['show_filters'] && $sort) {
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'bedrooms':
                    $query->orderBy('bedrooms', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('listing_date', 'desc');
                    break;
            }
        }

        $data['properties'] = $query->limit($data['limit'] ?? 6)->get();
        $data['has_properties'] = $data['properties']->count() > 0;

        return $data;
    }
}