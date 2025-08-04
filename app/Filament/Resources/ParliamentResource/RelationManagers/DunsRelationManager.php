<?php

namespace App\Filament\Resources\ParliamentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DunsRelationManager extends RelationManager
{
    protected static string $relationship = 'duns'; // dari model Parliament::duns()

    protected static ?string $recordTitleAttribute = 'dun';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('u_dun_id')->required(),
            Forms\Components\TextInput::make('dun')->required(),
            Forms\Components\TextInput::make('code_dun')->required(),
            Forms\Components\TextInput::make('code_parliament')->required(),
            Forms\Components\TextInput::make('code_state')->required(),
            Forms\Components\TextInput::make('code_dun2')->required(),
            Forms\Components\Hidden::make('u_parliament_id'), // diisi automatik
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('u_dun_id'),
                Tables\Columns\TextColumn::make('dun'),
                Tables\Columns\TextColumn::make('code_dun'),
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
            ]);
    }
}
