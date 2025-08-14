<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentEmergencyContactResource\Pages;
use App\Filament\Resources\StudentEmergencyContactResource\RelationManagers;
use App\Models\StudentEmergencyContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentEmergencyContactResource extends Resource
{
    protected static ?string $model = StudentEmergencyContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('matric_id')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('full_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('relationship'),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('alt_phone_number')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('is_primary'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matric_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('relationship'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alt_phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_primary'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentEmergencyContacts::route('/'),
            'create' => Pages\CreateStudentEmergencyContact::route('/create'),
            'edit' => Pages\EditStudentEmergencyContact::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
