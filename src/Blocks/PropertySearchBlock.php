<?php

namespace TallCms\RealEstate\Blocks;

use App\TallCms\PageBuilder\AbstractBlock;
use TallCms\RealEstate\Models\Property;
use TallCms\RealEstate\Models\PropertyType;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;

class PropertySearchBlock extends AbstractBlock
{
    public function getDisplayName(): string
    {
        return 'Property Search & Listings';
    }

    public function getDescription(): string
    {
        return 'Search and display property listings with filters and pagination';
    }

    public function getCategory(): string
    {
        return 'Real Estate';
    }

    public function getIcon(): string
    {
        return 'heroicon-o-magnifying-glass';
    }

    public function getDefaults(): array
    {
        return [
            'title' => 'Find Your Dream Property',
            'subtitle' => 'Search through our extensive collection of properties',
            'show_search_form' => true,
            'show_filters' => true,
            'properties_per_page' => 9,
            'default_sort' => 'latest',
            'show_featured_only' => false,
            'property_types' => [], // Empty means all types
            'max_price' => null,
            'min_price' => null,
            'search_placeholder' => 'Enter location, property type, or keywords...',
            'no_results_message' => 'No properties found matching your criteria.',
            'view_style' => 'grid', // grid or list
            'show_map' => false,
            'enable_favorites' => false,
        ];
    }

    public function getSchema(): array
    {
        return [
            Section::make('Content')
                ->description('Search form titles and messaging')
                ->schema([
                    TextInput::make('title')
                        ->label('Block Title')
                        ->placeholder('Find Your Dream Property'),
                    
                    Textarea::make('subtitle')
                        ->label('Subtitle/Description')
                        ->rows(2)
                        ->placeholder('Search through our extensive collection of properties'),
                    
                    TextInput::make('search_placeholder')
                        ->label('Search Input Placeholder')
                        ->placeholder('Enter location, property type, or keywords...'),
                    
                    Textarea::make('no_results_message')
                        ->label('No Results Message')
                        ->rows(2)
                        ->placeholder('No properties found matching your criteria.'),
                ])
                ->collapsible(),

            Section::make('Search & Filter Settings')
                ->description('Configure search functionality and filters')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Toggle::make('show_search_form')
                                ->label('Show Search Form')
                                ->default(true)
                                ->helperText('Display the main search input'),
                            
                            Toggle::make('show_filters')
                                ->label('Show Advanced Filters')
                                ->default(true)
                                ->helperText('Display property type, price, and other filters'),
                        ]),
                    
                    Grid::make(2)
                        ->schema([
                            Select::make('property_types')
                                ->label('Allowed Property Types')
                                ->multiple()
                                ->options(PropertyType::getSelectOptions())
                                ->helperText('Leave empty to show all property types'),
                            
                            Select::make('default_sort')
                                ->label('Default Sort Order')
                                ->options([
                                    'latest' => 'Latest Listed',
                                    'price_low' => 'Price: Low to High',
                                    'price_high' => 'Price: High to Low',
                                    'bedrooms' => 'Most Bedrooms',
                                    'square_footage' => 'Largest Square Footage',
                                ])
                                ->default('latest'),
                        ]),
                    
                    Grid::make(3)
                        ->schema([
                            TextInput::make('min_price')
                                ->label('Minimum Price Filter')
                                ->numeric()
                                ->prefix('$')
                                ->helperText('Pre-filter properties above this price'),
                            
                            TextInput::make('max_price')
                                ->label('Maximum Price Filter')
                                ->numeric()
                                ->prefix('$')
                                ->helperText('Pre-filter properties below this price'),
                            
                            Toggle::make('show_featured_only')
                                ->label('Featured Properties Only')
                                ->helperText('Only show featured listings'),
                        ]),
                ])
                ->collapsible(),

            Section::make('Display Settings')
                ->description('Control how properties are displayed')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('properties_per_page')
                                ->label('Properties Per Page')
                                ->options([
                                    6 => '6 properties',
                                    9 => '9 properties',
                                    12 => '12 properties',
                                    15 => '15 properties',
                                    24 => '24 properties',
                                ])
                                ->default(9),
                            
                            Select::make('view_style')
                                ->label('Layout Style')
                                ->options([
                                    'grid' => 'Grid View',
                                    'list' => 'List View',
                                    'cards' => 'Card View',
                                ])
                                ->default('grid'),
                            
                            Toggle::make('show_map')
                                ->label('Show Map View')
                                ->helperText('Display properties on a map (requires coordinates)'),
                        ]),
                    
                    Toggle::make('enable_favorites')
                        ->label('Enable Favorites')
                        ->helperText('Allow users to save favorite properties'),
                ])
                ->collapsible(),

            Section::make('Styling')
                ->description('Customize the appearance of the search block')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            ColorPicker::make('search_bg_color')
                                ->label('Search Form Background')
                                ->default('#ffffff'),
                            
                            ColorPicker::make('button_color')
                                ->label('Search Button Color')
                                ->default('#3B82F6'),
                        ]),
                    
                    Grid::make(2)
                        ->schema([
                            ColorPicker::make('card_bg_color')
                                ->label('Property Card Background')
                                ->default('#ffffff'),
                            
                            Select::make('card_border_style')
                                ->label('Card Border Style')
                                ->options([
                                    'none' => 'No Border',
                                    'light' => 'Light Border',
                                    'medium' => 'Medium Border',
                                    'shadow' => 'Drop Shadow',
                                ])
                                ->default('light'),
                        ]),
                ])
                ->collapsible(),

            // Include built-in spacing and animation settings
            self::getSpacingFormSchema(),
            self::getAnimationFormSchema(),
        ];
    }

    /**
     * Process block data before rendering
     */
    public function processData(array $data): array
    {
        // For the new Livewire implementation, we just pass configuration
        // The Livewire component handles all the search and filtering logic
        
        return array_merge($data, [
            'component_config' => [
                'perPage' => $data['properties_per_page'] ?? 9,
                'defaultViewMode' => 'grid',
                'enableAnimations' => true,
                'showViewToggle' => true,
                'enableDistricts' => true,
                'enablePriceFilters' => true,
                'enableAdvancedFilters' => true,
                'searchPlaceholder' => $data['search_placeholder'] ?? 'Search properties...',
                'accentColor' => '#3B82F6',
                'backgroundColor' => '#ffffff',
            ]
        ]);
    }

    /**
     * Get the view path for this block
     */
    public function getViewPath(): string
    {
        // Try theme-specific template first, then fall back to plugin default
        $blockName = self::getName();
        
        if (view()->exists("themes.{$this->theme}.templates.blocks.{$blockName}")) {
            return "themes.{$this->theme}.templates.blocks.{$blockName}";
        }
        
        if (view()->exists("real-estate::blocks.{$blockName}")) {
            return "real-estate::blocks.{$blockName}";
        }
        
        return "tall-cms.blocks.{$blockName}";
    }
}