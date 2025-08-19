<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;

use App\Models\Subject;

use App\Enum\SubjectStatusEnum;
use App\Enum\StatusEnum;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    

    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject_code')
                    ->label('Subject Code')
                    ->required(),
                Forms\Components\TextInput::make('subject_name')
                    ->label('Subject Name')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('semester')
                    ->numeric()
                    ->required()
                    ->maxlength(1),
                Forms\Components\TextInput::make('credit_hour')
                    ->numeric()
                    ->required()
                    ->maxlength(1),
                Forms\Components\Select::make('subject_status')
                    ->required()
                    ->options(SubjectStatusEnum::class),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(StatusEnum::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester')
                    ->formatStateUsing(fn ($state) => 'Semester ' . $state)
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('credit_hour'),

                Tables\Columns\TextColumn::make('subject_status')
                    ->formatStateUsing(fn ($state) => strtoupper($state->value ?? (string) $state)),


                Tables\Columns\TextColumn::make('status'),

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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
            'import' => Pages\ImportSubjects::route('/import'),
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
        return 3;
    }

}
