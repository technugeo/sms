<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstituteResource\Pages;
use App\Filament\Resources\InstituteResource\RelationManagers;

use App\Models\Institute;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Parliament;
use App\Models\Dun;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstituteResource extends Resource
{
   

    protected static ?string $model = Institute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('mqa_institute_id')
                    ->label('Institute ID')
                    ->required(),
                Forms\Components\Select::make('category')
                    ->relationship('category_institute', 'category')
                    ->label('Category')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('abbreviation')
                    ->required(),
                Forms\Components\Select::make('country')
                    ->label('Country')
                    ->options(Country::pluck('name', 'name')->toArray())
                    ->searchable()
                    ->preload()
                    ->reactive(),

                Forms\Components\Select::make('state')
                    ->label('State')
                    ->options(fn (callable $get) =>
                        $get('country')
                            ? State::where('country_id', Country::where('name', $get('country'))->value('id'))
                                ->pluck('name', 'name')
                                ->toArray()
                            : [])
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->disabled(fn (callable $get) => !$get('country')),

                Forms\Components\Select::make('district')
                    ->label('District')
                    ->options(fn (callable $get) =>
                        $get('state')
                            ? City::where('state_id', State::where('name', $get('state'))->value('id'))
                                ->pluck('name', 'name')
                                ->toArray()
                            : [])
                    ->searchable()
                    ->preload()
                    ->disabled(fn (callable $get) => !$get('state')),

                
                Forms\Components\Select::make('parliament')
                    ->label('Parliament')
                    ->options(
                        Parliament::pluck('parliament', 'parliament')->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('dun')
                    ->label('Dun')
                    ->options(fn (callable $get) =>
                        $get('parliament')
                            ? Dun::where('parliament_id', Parliament::where('parliament', $get('parliament'))->value('id'))
                                ->pluck('dun', 'dun') 
                                ->toArray()
                            : [])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (callable $get) => !$get('parliament')),

                    
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mqa_institute_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abbreviation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_institute.category')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parliament')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dun')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => true),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DepartmentsRelationManager::class,
            RelationManagers\FacultyRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutes::route('/'),
            'create' => Pages\CreateInstitute::route('/create'),
            'import' => Pages\ImportInstitutes::route('/import'),
            'view' => Pages\ViewInstitute::route('/{record}'),
            'edit' => Pages\EditInstitute::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Data Configuration';
    }
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getBreadcrumbs(array $parameters = []): array
    {
        if (isset($parameters['record'])) {
            $record = static::resolveRecord($parameters['record']);

            return [
                static::getUrl() => 'Institutes',
                static::getUrl('edit', ['record' => $record]) => $record->name,
            ];
        }

        return [
            static::getUrl() => 'Institutes',
        ];
    }


}
