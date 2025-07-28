<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubregionResource\Pages;
use App\Filament\Resources\SubregionResource\RelationManagers;
use App\Models\Subregion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubregionResource extends Resource
{
    protected static ?string $model = Subregion::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $navigationGroup = 'World';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('translations')
                        ->helperText('Enter translations as a JSON string or key-value pairs.')
                        ->json()
                        ->columnSpanFull(),
                    Forms\Components\Select::make('region_id')
                        ->relationship('region', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
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
                Tables\Columns\TextColumn::make('region.name')
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
            RelationManagers\CountriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubregions::route('/'),
            'create' => Pages\CreateSubregion::route('/create'),
            'edit' => Pages\EditSubregion::route('/{record}/edit'),
        ];
    }
}
