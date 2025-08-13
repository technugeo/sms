<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use App\Models\StudentGuardian;

class GuardianRelationManager extends RelationManager
{
    protected static string $relationship = 'studentGuardians'; 
    protected static ?string $recordTitleAttribute = 'full_name';

    // Non-static method
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('guardian_type')->required()->maxLength(100),
                Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('ic_passport_no')->required()->maxLength(20),
                Forms\Components\TextInput::make('nationality')->maxLength(100),
                Forms\Components\Textarea::make('address')->columnSpanFull(),
                Forms\Components\TextInput::make('phone_hp')->tel()->maxLength(12),
                Forms\Components\TextInput::make('phone_house')->tel()->maxLength(12),
                Forms\Components\TextInput::make('phone_office')->tel()->maxLength(12),
                Forms\Components\TextInput::make('email')->email()->maxLength(100),
                Forms\Components\TextInput::make('occupation')->maxLength(100),
                Forms\Components\TextInput::make('monthly_income')->numeric(),
                Forms\Components\Checkbox::make('is_emergency_contact'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guardian_type')->searchable(),
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('ic_passport_no')->searchable(),
                Tables\Columns\TextColumn::make('phone_hp')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('monthly_income')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('is_emergency_contact'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
