<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParliamentsRelationManager extends RelationManager
{
    protected static string $relationship = 'parliaments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('u_parliament_id')->required(),
                Forms\Components\TextInput::make('parliament')->required(),
                Forms\Components\TextInput::make('code_parliament')->numeric()->required(),
                Forms\Components\TextInput::make('u_state_id')->numeric()->required(),
                Forms\Components\TextInput::make('state_id')->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('u_parliament_id'),
                Tables\Columns\TextColumn::make('parliament'),
                Tables\Columns\TextColumn::make('code_parliament'),
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
