<?php

namespace App\Filament\Resources;

use Spatie\Permission\Models\Role;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MultiSelect;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;

use App\Filament\Resources\RoleResource\Pages;

use Illuminate\Support\Collection;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
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
            MultiSelect::make('permissions')
                ->relationship('permissions', 'name')
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),

                // Unique guard names in badge
                BadgeColumn::make('permissions_guard_name')
                    ->label('Guard')
                    ->getStateUsing(fn ($record) => $record->permissions->pluck('guard_name')->unique()->join(', '))
                    ->colors([
                        'primary' => fn ($state) => $state === 'web',
                        'secondary' => fn ($state) => $state !== 'web',
                    ]),
                    
                // Permissions count in badge
                BadgeColumn::make('permissions_count')
                    ->label('Permissions')
                    ->getStateUsing(fn ($record) => $record->permissions->count())
                    ->colors([
                        'primary' => fn ($state) => $state > 0, // blue if >0
                        'secondary' => fn ($state) => $state == 0, // gray if 0
                    ]),

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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
