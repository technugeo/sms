<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParliamentResource\Pages;
use App\Filament\Resources\ParliamentResource\RelationManagers\DunsRelationManager;
use App\Models\Parliament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParliamentResource extends Resource
{
    protected static ?string $model = Parliament::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'World';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('u_parliament_id')->required(),
                    Forms\Components\TextInput::make('parliament')->required(),
                    Forms\Components\TextInput::make('code_parliament')->numeric()->required(),
                    Forms\Components\TextInput::make('u_state_id')->numeric()->required(),
                    Forms\Components\TextInput::make('state_id')->default(1),
                ])
                ->columns(2)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('u_parliament_id'),
            Tables\Columns\TextColumn::make('parliament'),
            Tables\Columns\TextColumn::make('code_parliament'),
        ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DunsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParliaments::route('/'),
            'create' => Pages\CreateParliament::route('/create'),
            'edit' => Pages\EditParliament::route('/{record}/edit'),
        ];
    }
    public static function canSee(): bool
    {
        return auth()->user()->role === 'SA';
    }
}
