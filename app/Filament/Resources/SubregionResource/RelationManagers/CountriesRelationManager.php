<?php

namespace App\Filament\Resources\SubregionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountriesRelationManager extends RelationManager
{
    protected static string $relationship = 'countries';

    public function form(Form $form): Form
    {
        return $form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
                Tables\Columns\IconColumn::make('flag')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
