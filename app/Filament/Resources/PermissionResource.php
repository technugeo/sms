<?php

namespace App\Filament\Resources;

use Spatie\Permission\Models\Permission;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;

use App\Filament\Resources\PermissionResource\Pages;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Setting';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SA'); // example
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('guard_name'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                DeleteAction::make(), // single row delete
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->action(fn ($records) => $records->each->delete())
                    ->requiresConfirmation()
                    ->color('danger'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
