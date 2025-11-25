<?php

namespace TallCms\RealEstate\Resources;

use TallCms\RealEstate\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Real Estate Settings';
    protected static ?string $navigationLabel = 'Districts';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('District Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', \Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(District::class, 'slug', ignoreRecord: true)
                            ->rules(['regex:/^[a-z0-9-]+$/']),

                        Forms\Components\Select::make('country')
                            ->required()
                            ->options([
                                'Singapore' => 'Singapore',
                                'Malaysia' => 'Malaysia',
                                'Thailand' => 'Thailand',
                                'Indonesia' => 'Indonesia',
                                'Philippines' => 'Philippines',
                                'Vietnam' => 'Vietnam',
                                'Hong Kong' => 'Hong Kong',
                                'Taiwan' => 'Taiwan',
                            ])
                            ->default('Singapore')
                            ->searchable(),

                        Forms\Components\TextInput::make('state_province')
                            ->maxLength(255)
                            ->placeholder('e.g., Selangor, Bangkok Metropolitan'),

                        Forms\Components\TextInput::make('postal_code_prefix')
                            ->maxLength(10)
                            ->placeholder('e.g., 01, 10')
                            ->helperText('Common postal code prefix for this district'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Only active districts will be available for selection'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Singapore' => 'success',
                        'Malaysia' => 'warning',
                        'Thailand' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('state_province')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('postal_code_prefix')
                    ->label('Postal Prefix')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('properties_count')
                    ->counts('properties')
                    ->label('Properties')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                
                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'Singapore' => 'Singapore',
                        'Malaysia' => 'Malaysia',
                        'Thailand' => 'Thailand',
                        'Indonesia' => 'Indonesia',
                        'Philippines' => 'Philippines',
                        'Vietnam' => 'Vietnam',
                        'Hong Kong' => 'Hong Kong',
                        'Taiwan' => 'Taiwan',
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
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
            'index' => DistrictResource\Pages\ListDistricts::route('/'),
            'create' => DistrictResource\Pages\CreateDistrict::route('/create'),
            'view' => DistrictResource\Pages\ViewDistrict::route('/{record}'),
            'edit' => DistrictResource\Pages\EditDistrict::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}