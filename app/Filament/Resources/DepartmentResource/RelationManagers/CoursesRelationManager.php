<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use App\Enum\ProgrammeEnum;
use App\Enum\StatusEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prog_name')
            ->columns([
                Tables\Columns\TextColumn::make('prog_code')
                    ->label('Course Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prog_name')
                    ->label('Course Name')
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
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['faculty_id'] = $this->ownerRecord->faculty_id ?? null;
                        $data['created_by'] = auth()->user()->email; // or id
                        $data['updated_by'] = auth()->user()->email;

                        return $data;
                    })
                    ->using(function (array $data) {
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->user()->email; // or id
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])

            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.courses.view', $record))
                    ->openUrlInNewTab(false),
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
