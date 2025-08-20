<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\Course;
use App\Models\Department;

use App\Enum\ProgrammeEnum;
use App\Enum\StatusEnum;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('prog_code')
                    ->label('Course Code')
                    ->required(),
                Forms\Components\TextInput::make('prog_name')
                    ->label('Course Name')
                    ->required()
                    ->maxLength(255),

                // Forms\Components\Select::make('faculty_id')
                //     ->relationship('faculty', 'name') // use the existing relation in Course model
                //     ->label('Faculty')
                //     ->required(),

                Forms\Components\Select::make('programme_type')
                    ->label('Programme Type')
                    ->required()
                    ->options(fn () => collect(ProgrammeEnum::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                        ->toArray()),

                Forms\Components\TextInput::make('sponsoring_body')
                    ->label('Sponsor')
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options(StatusEnum::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('faculty.name')
                    ->label('Faculty'),

                Tables\Columns\TextColumn::make('prog_code')
                    ->label('Programme Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prog_name')
                    ->label('Programme Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('programme_type')
                    ->label('Programme Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sponsoring_body')
                    ->label('Sponsor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
            // RelationManagers\SemestersRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
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
        return 2;
    }
}
