<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'World';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('iso3')
                                    ->maxLength(3),
                                Forms\Components\TextInput::make('numeric_code')
                                    ->maxLength(3),
                                Forms\Components\TextInput::make('iso2')
                                    ->maxLength(2),
                                Forms\Components\TextInput::make('phonecode')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('capital')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('currency')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('currency_name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('currency_symbol')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tld')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('native')
                                    ->maxLength(255),
                                Forms\Components\Select::make('subregion_id')
                                    ->relationship('subregion', 'name')
                                    ->nullable()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('nationality')
                                    ->maxLength(255),
                            ]),


                        Forms\Components\KeyValue::make('timezones')
                            ->helperText('Enter timezones as a JSON string or array.')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('translations')
                            ->helperText('Enter translations as a JSON string or key-value pairs.')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->extraAttributes(['inputmode' => 'decimal']),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->extraAttributes(['inputmode' => 'decimal']),
                        Forms\Components\TextInput::make('emoji')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('emojiU')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('flag')
                            ->default(true)
                            ->required(),
                        Forms\Components\TextInput::make('wikiDataId')
                            ->maxLength(255),
                    ])
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iso2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capital')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subregion.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('flag')
                    ->boolean(),
                Tables\Columns\TextColumn::make('wikiDataId')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->relationship('region', 'name'),
                Tables\Filters\SelectFilter::make('subregion_id')
                    ->relationship('subregion', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
