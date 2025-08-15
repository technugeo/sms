<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Student;
use App\Models\Country;
use App\Models\StudentGuardian;

use App\Enum\CitizenEnum;
use App\Enum\MarriageEnum;
use App\Enum\AcademicEnum;
use App\Enum\NationalityEnum;
use App\Enum\GenderEnum;
use App\Enum\RaceEnum;
use App\Enum\ReligionEnum;
use App\Enum\IntakeEnum;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    
    public static function canSee(): bool
    {
        return in_array(auth()->user()->role, ['SA', 'AA', 'NAO', 'AO']);
    }

    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('current_course')
                    ->label('Course')
                    ->options(function () {
                        return \App\Models\Course::all()->mapWithKeys(function ($course) {
                            return [$course->prog_code => "{$course->prog_code} - {$course->prog_name}"];
                        })->toArray();
                    })
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                Forms\Components\Select::make('intake_month')
                    ->required()
                    ->options(IntakeEnum::class),

                Forms\Components\TextInput::make('intake_year')
                    ->required()
                    ->maxLength(4),

                Forms\Components\TextInput::make('full_name')
                    ->required(),

                Forms\Components\TextInput::make('nric')
                    ->required()
                    ->maxLength(12),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->afterStateHydrated(function ($component, $record, $state) {
                        if ($record) {
                            // Only set the state when editing an existing student
                            $component->state($record->email);
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state),




                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->numeric(),

                // Nationality (saved as country name to `students` table)
                Forms\Components\Select::make('nationality')
                    ->label('Nationality')
                    ->options(Country::pluck('name', 'name')->toArray())
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('passport_no'),

                Forms\Components\Select::make('gender')
                    ->required()
                    ->options(GenderEnum::class),

                Forms\Components\Select::make('marriage_status')
                    ->required()
                    ->options(MarriageEnum::class),

                Forms\Components\Select::make('race')
                    ->required()
                    ->options(RaceEnum::class),

                Forms\Components\Select::make('religion')
                    ->required()
                    ->options(ReligionEnum::class),

                Forms\Components\Select::make('citizen')
                    ->label('Bumiputera')
                    ->required()
                    ->options(CitizenEnum::class),

                Forms\Components\Select::make('nationality_type')
                    ->required()
                    ->options(NationalityEnum::class),

                Forms\Components\Select::make('academic_status')
                    ->required()
                    ->options(AcademicEnum::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matric_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('nric')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passport_no')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('nationality_type'),
                Tables\Columns\TextColumn::make('citizen'),
                Tables\Columns\TextColumn::make('marriage_status'),
                
                Tables\Columns\TextColumn::make('course') 
                    ->label('Course')
                    ->formatStateUsing(function ($record) {
                        return $record->course ? $record->course->prog_code . ' - ' . $record->course->prog_name : null;
                    }),
                    
                Tables\Columns\TextColumn::make('user.status')
                    ->label('Account status'),
                Tables\Columns\TextColumn::make('academic_status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('updateAcademicStatus')
                    ->label('Update Status')
                    ->icon('heroicon-m-adjustments-vertical')
                    ->form([
                        Forms\Components\Select::make('academic_status')
                            ->label('Academic Status')
                            ->options(AcademicEnum::class)
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'academic_status' => $data['academic_status'],
                        ]);

                        if (strtolower($data['academic_status']) === 'suspended') {
                            if ($record->user) {
                                $record->user->update([
                                    'status' => 'Suspended',
                                ]);
                            }
                        }
                        Notification::make()
                            ->title('Academic status updated')
                            ->success()
                            ->body("{$record->full_name}'s academic status was changed successfully.")
                            ->send();
                    })
                    ->modalHeading('Update Academic Status')
                    ->modalButton('Update')
                    ->requiresConfirmation(false),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        
        return [
            RelationManagers\LocalAddressRelationManager::class,
            RelationManagers\ForeignAddressRelationManager::class,
            RelationManagers\GuardianRelationManager::class,
            RelationManagers\EmergencyContactRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'import' => Pages\ImportStudents::route('/import'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with('course');  // eager load course relation
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }

}
