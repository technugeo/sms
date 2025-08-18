<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;


use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

use App\Models\StudentEduhistory;
use App\Models\Country;

use App\Enum\EducationLevelEnum;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\Page;

class StudentEduhistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'eduHistories'; // make sure this matches the relation in Student model
    protected static ?string $recordTitleAttribute = 'institution_name'; // title attribute

    
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Education History';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('institution_name')
                    ->label('Institution Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('country')
                    ->label('Country')
                    ->options(Country::pluck('name', 'name')->toArray())
                    ->searchable()
                    ->preload()
                    ->reactive(),
                Forms\Components\Select::make('level')
                    ->required()
                    ->options(EducationLevelEnum::class),
                Forms\Components\TextInput::make('subject_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('grade')
                    ->maxLength(10),
                Forms\Components\TextInput::make('programme_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cgpa')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(9.99)
                    ->helperText('Max 3 digits, 2 after decimal'),
                Forms\Components\TextInput::make('start_year')
                    ->numeric()
                    ->maxLength(4),
                Forms\Components\TextInput::make('end_year')
                    ->numeric()
                    ->maxLength(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('institution_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\TextColumn::make('level')->sortable(),
                Tables\Columns\TextColumn::make('subject_name')->sortable(),
                Tables\Columns\TextColumn::make('grade')->sortable(),
                Tables\Columns\TextColumn::make('programme_name')->sortable(),
                Tables\Columns\TextColumn::make('cgpa')->sortable(),
                Tables\Columns\TextColumn::make('start_year')->sortable(),
                Tables\Columns\TextColumn::make('end_year')->sortable(),
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
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ]);
    }
}
