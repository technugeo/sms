<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentGuardianResource\Pages;
use App\Filament\Resources\StudentGuardianResource\RelationManagers;
use App\Models\StudentGuardian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentGuardianResource extends Resource
{
    protected static ?string $model = StudentGuardian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('matric_id')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('guardian_type'),
                Forms\Components\TextInput::make('full_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ic_passport_no')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('nationality')
                    ->maxLength(100),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone_hp')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('phone_house')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('phone_office')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('occupation')
                    ->maxLength(100),
                Forms\Components\TextInput::make('monthly_income')
                    ->numeric(),
                Forms\Components\TextInput::make('is_emergency_contact'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matric_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guardian_type'),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ic_passport_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_house')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_office')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monthly_income')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_emergency_contact'),
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
            'index' => Pages\ListStudentGuardians::route('/'),
            'create' => Pages\CreateStudentGuardian::route('/create'),
            'edit' => Pages\EditStudentGuardian::route('/{record}/edit'),
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
