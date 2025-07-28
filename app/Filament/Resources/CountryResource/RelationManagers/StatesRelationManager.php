<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatesRelationManager extends RelationManager
{
    protected static string $relationship = 'states';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_code')
                    ->required()
                    ->maxLength(2),
                Forms\Components\TextInput::make('fips_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('iso2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(191),
                Forms\Components\TextInput::make('level')
                    ->numeric(),
                Forms\Components\TextInput::make('parent_id')
                    ->numeric(),
                Forms\Components\TextInput::make('native')
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->step(0.00000001)
                    ->extraAttributes(['inputmode' => 'decimal']),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->step(0.00000001)
                    ->extraAttributes(['inputmode' => 'decimal']),
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
                Tables\Columns\TextColumn::make('country_code')
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
