<?php

namespace TallCms\RealEstate\Resources;

use TallCms\RealEstate\Models\Property;
use TallCms\RealEstate\Models\PropertyType;
use TallCms\RealEstate\Models\District;
use TallCms\RealEstate\Models\Amenity;
use TallCms\RealEstate\Models\Feature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use TallCms\RealEstate\Resources\PropertyResource\Pages;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?string $modelLabel = 'Property';

    protected static ?string $pluralModelLabel = 'Properties';

    protected static ?string $navigationGroup = 'Real Estate';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📋 Basic Property Information')
                    ->description('Start with the essential details about your property')
                    ->schema([
                        TextInput::make('title')
                            ->label('Property Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Beautiful 3BR Condo in Orchard')
                            ->helperText('Create an attractive title that highlights key features')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, callable $set) {
                                if ($context === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique(Property::class, 'slug', ignoreRecord: true)
                            ->helperText('Auto-generated from title. Used in the property URL.')
                            ->dehydrated(fn ($state) => filled($state))
                            ->hidden(fn (string $operation): bool => $operation === 'create'),

                        Grid::make(2)
                            ->schema([
                                Select::make('property_type_id')
                                    ->label('🏠 Property Type')
                                    ->required()
                                    ->relationship('propertyType', 'name')
                                    ->options(PropertyType::getSelectOptions())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $data['slug'] = \Str::slug($data['name']);
                                        return PropertyType::create($data)->getKey();
                                    }),

                                Select::make('listing_status')
                                    ->label('📈 Listing Status')
                                    ->required()
                                    ->options([
                                        'active' => '✅ Active - Ready to Show',
                                        'pending' => '⏳ Pending - Under Negotiation',
                                        'sold' => '✅ Sold',
                                        'rented' => '🏠 Rented',
                                        'off_market' => '❌ Off Market',
                                    ])
                                    ->default('active')
                                    ->native(false),
                            ]),

                        Grid::make(1)
                            ->schema([
                                Select::make('tenure')
                                    ->label('🏛️ Property Tenure')
                                    ->placeholder('Select tenure type')
                                    ->options([
                                        'Freehold' => '🏆 Freehold',
                                        '99-year' => '📅 99-year Leasehold',
                                        '999-year' => '📅 999-year Leasehold',
                                        '103-year' => '📅 103-year Leasehold',
                                        'Leasehold' => '📅 Other Leasehold',
                                    ])
                                    ->native(false)
                                    ->helperText('Property ownership type - important for resale and financing'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('price')
                                    ->label('💰 Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S$')
                                    ->placeholder('1500000')
                                    ->helperText('Enter the selling/rental price'),

                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        'SGD' => '🇸🇬 SGD (Singapore Dollar)',
                                        'MYR' => '🇲🇾 MYR (Malaysian Ringgit)',
                                        'THB' => '🇹🇭 THB (Thai Baht)',
                                        'USD' => '🇺🇸 USD (US Dollar)',
                                        'EUR' => '🇪🇺 EUR (Euro)',
                                        'GBP' => '🇬🇧 GBP (British Pound)',
                                    ])
                                    ->default('SGD')
                                    ->native(false),
                            ]),

                        Textarea::make('description')
                            ->label('Property Description')
                            ->placeholder('Describe the property highlights, location benefits, and unique features...')
                            ->rows(4)
                            ->maxLength(2000)
                            ->helperText('Write a compelling description that will attract potential buyers/renters')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('📍 Location & Address')
                    ->description('Help buyers find this property easily')
                    ->schema([
                        TextInput::make('address')
                            ->label('Full Address')
                            ->required()
                            ->placeholder('e.g., 123 Orchard Road, #12-34')
                            ->helperText('Include unit number if applicable')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('district_id')
                                    ->label('🏘️ District/Area')
                                    ->relationship('district', 'name')
                                    ->options(District::getSelectOptions())
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Choose the property district')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('country')
                                            ->options([
                                                'Singapore' => 'Singapore',
                                                'Malaysia' => 'Malaysia',
                                                'Thailand' => 'Thailand',
                                            ])
                                            ->default('Singapore'),
                                        Forms\Components\TextInput::make('postal_code_prefix'),
                                        Forms\Components\Textarea::make('description'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $data['slug'] = \Str::slug($data['name']);
                                        return District::create($data)->getKey();
                                    }),

                                Select::make('country')
                                    ->label('🌏 Country')
                                    ->options([
                                        'Singapore' => '🇸🇬 Singapore',
                                        'Malaysia' => '🇲🇾 Malaysia',
                                        'Thailand' => '🇹🇭 Thailand',
                                        'Indonesia' => '🇮🇩 Indonesia',
                                        'Philippines' => '🇵🇭 Philippines',
                                        'Vietnam' => '🇻🇳 Vietnam',
                                    ])
                                    ->default('Singapore')
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('district_id', null); // Clear district when country changes
                                    }),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('City')
                                    ->required()
                                    ->placeholder('e.g., Singapore, Kuala Lumpur')
                                    ->helperText('Main city where property is located'),

                                TextInput::make('state')
                                    ->label('State/Province')
                                    ->placeholder('Optional (not used in Singapore)')
                                    ->helperText('Only required for countries with states'),

                                TextInput::make('zip_code')
                                    ->label('Postal Code')
                                    ->required()
                                    ->placeholder('e.g., 238863')
                                    ->helperText('6-digit Singapore postal code'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('📍 Latitude')
                                    ->numeric()
                                    ->placeholder('1.3048')
                                    ->helperText('Optional: Precise map coordinates'),

                                TextInput::make('longitude')
                                    ->label('📍 Longitude')
                                    ->numeric()
                                    ->placeholder('103.8318')
                                    ->helperText('Optional: Precise map coordinates'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('🏠 Property Specifications')
                    ->description('Key details that buyers care about most')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('bedrooms')
                                    ->label('🛏️ Bedrooms')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('3')
                                    ->helperText('Number of bedrooms'),

                                TextInput::make('bathrooms')
                                    ->label('🚿 Bathrooms')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->placeholder('2')
                                    ->helperText('Include 0.5 for powder rooms'),

                                TextInput::make('half_bathrooms')
                                    ->label('🚽 Half Baths')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('1')
                                    ->helperText('Powder rooms without shower'),

                                TextInput::make('garage_spaces')
                                    ->label('🚗 Parking')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('2')
                                    ->helperText('Parking spaces/garage slots'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('square_footage')
                                    ->label('📐 Floor Area (sq ft)')
                                    ->numeric()
                                    ->suffix('sq ft')
                                    ->placeholder('1200')
                                    ->helperText('Total built-up area'),

                                TextInput::make('lot_size')
                                    ->label('🌳 Land Area')
                                    ->numeric()
                                    ->suffix('sq ft')
                                    ->placeholder('2000')
                                    ->helperText('Land area for landed properties'),

                                TextInput::make('year_built')
                                    ->label('🏗️ Built Year')
                                    ->numeric()
                                    ->minValue(1960)
                                    ->maxValue(date('Y'))
                                    ->placeholder(date('Y'))
                                    ->helperText('Year of completion'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('✨ What Makes This Property Special')
                    ->description('Highlight the best features and amenities to attract buyers')
                    ->schema([
                        Forms\Components\CheckboxList::make('propertyAmenities')
                            ->label('🏢 Building & Facilities Amenities')
                            ->relationship('propertyAmenities', 'name')
                            ->options(Amenity::getSelectOptions())
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->helperText('Select amenities available in the building/development')
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('propertyFeatures')
                            ->label('🏠 Property Features')
                            ->relationship('propertyFeatures', 'name')
                            ->options(Feature::getSelectOptions())
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->helperText('Select features specific to this unit/property')
                            ->columnSpanFull(),

                        Forms\Components\Section::make('📋 Legacy Data (Migration Only)')
                            ->description('For properties being migrated from old system')
                            ->schema([
                                TagsInput::make('amenities')
                                    ->label('Legacy Amenities (JSON)')
                                    ->placeholder('Leave empty for new properties')
                                    ->helperText('⚠️ This field will be removed - use checkboxes above'),

                                TagsInput::make('features')
                                    ->label('Legacy Features (JSON)')
                                    ->placeholder('Leave empty for new properties')
                                    ->helperText('⚠️ This field will be removed - use checkboxes above'),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('📸 Photos & Virtual Experience')
                    ->description('Great photos sell properties - showcase your best angles!')
                    ->schema([
                        FileUpload::make('photos')
                            ->label('🖼️ Property Photos')
                            ->multiple()
                            ->image()
                            ->directory('property-photos')
                            ->maxFiles(20)
                            ->maxSize(5120) // 5MB max per file (same as HeroBlock)
                            ->reorderable()
                            ->imagePreviewHeight('250')
                            ->panelAspectRatio('16:9')
                            ->imageResizeMode('cover')
                            ->columnSpanFull()
                            ->helperText('📝 Pro tip: Upload high-quality images! First photo becomes the main listing image. Drag to reorder.'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('virtual_tour_url')
                                    ->label('🏠 Virtual Tour Link')
                                    ->url()
                                    ->placeholder('https://my.matterport.com/show/?m=...')
                                    ->helperText('360° virtual tour link (Matterport, etc.)'),

                                TextInput::make('video_url')
                                    ->label('🎬 Property Video')
                                    ->url()
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->helperText('YouTube or property walkthrough video'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('👤 Agent Contact Information')
                    ->description('Your contact details for buyer inquiries')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('agent_name')
                                    ->label('👨‍💼 Agent Name')
                                    ->placeholder('John Tan')
                                    ->helperText('Your full name for buyer contact'),

                                TextInput::make('agent_email')
                                    ->label('📧 Email Address')
                                    ->email()
                                    ->placeholder('john.tan@realestate.com')
                                    ->helperText('Email for property inquiries'),

                                TextInput::make('agent_phone')
                                    ->label('📱 Contact Number')
                                    ->tel()
                                    ->placeholder('+65 9123 4567')
                                    ->helperText('Mobile number for immediate contact'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('🚀 Listing Settings & Visibility')
                    ->description('Control when and how this property appears on your website')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('listing_date')
                                    ->label('📅 Listing Date')
                                    ->default(now())
                                    ->helperText('When this property was first listed'),

                                DatePicker::make('available_date')
                                    ->label('🗓️ Available From')
                                    ->helperText('When buyers can move in (optional)'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('⭐ Featured Property')
                                    ->helperText('✨ Highlight this property in featured listings and homepage')
                                    ->inline(false),

                                Toggle::make('is_published')
                                    ->label('🌐 Publish to Website')
                                    ->helperText('✅ Make this property visible to public (turn off for draft)')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('🔍 Search Engine Optimization')
                    ->description('Help buyers find this property on Google and search engines')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('📝 SEO Page Title')
                            ->maxLength(60)
                            ->placeholder('Beautiful 3BR Condo in Orchard - Prime Location')
                            ->helperText('📊 Leave empty to auto-generate from property title (Recommended)'),

                        Textarea::make('meta_description')
                            ->label('📄 SEO Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->placeholder('Stunning 3-bedroom condo in prime Orchard location. Modern amenities, city views...')
                            ->helperText('📊 Leave empty to auto-generate from property description (Recommended)'),

                        TagsInput::make('seo_keywords')
                            ->label('🏷️ SEO Keywords')
                            ->placeholder('Type keywords and press Enter')
                            ->helperText('🎯 Optional: Add relevant search terms like "condo", "Orchard", "MRT nearby"'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl('/images/placeholder-property.jpg'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('formatted_price')
                    ->label('Price')
                    ->sortable('price'),

                TextColumn::make('propertyType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'HDB Flat' => 'success',
                        'Condominium' => 'primary',
                        'Landed House' => 'warning',
                        'Executive Condominium' => 'info',
                        'Commercial' => 'danger',
                        'Industrial' => 'gray',
                        default => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('district.name')
                    ->label('District')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('full_address')
                    ->label('Location')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),

                TextColumn::make('bedrooms')
                    ->label('Beds')
                    ->sortable(),

                TextColumn::make('bathroom_display')
                    ->label('Baths'),

                BadgeColumn::make('listing_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'sold',
                        'secondary' => 'off_market',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

                BooleanColumn::make('is_featured')
                    ->label('Featured'),

                BooleanColumn::make('is_published')
                    ->label('Published'),

                TextColumn::make('created_at')
                    ->label('Listed')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('propertyType')
                    ->relationship('propertyType', 'name')
                    ->label('Property Type')
                    ->options(PropertyType::getSelectOptions())
                    ->searchable(),

                SelectFilter::make('district')
                    ->relationship('district', 'name')
                    ->label('District')
                    ->options(District::getSelectOptions())
                    ->searchable(),

                SelectFilter::make('listing_status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'off_market' => 'Off Market',
                    ]),

                SelectFilter::make('country')
                    ->options([
                        'Singapore' => 'Singapore',
                        'Malaysia' => 'Malaysia',
                        'Thailand' => 'Thailand',
                    ]),

                Filter::make('is_featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),

                Filter::make('is_published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),

                Filter::make('price_range')
                    ->form([
                        TextInput::make('price_from')
                            ->numeric()
                            ->placeholder('Min price'),
                        TextInput::make('price_to')
                            ->numeric()
                            ->placeholder('Max price'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->canManageContent() ?? false;
    }
}