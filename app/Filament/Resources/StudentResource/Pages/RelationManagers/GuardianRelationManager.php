<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Country;
use App\Enum\GuardianEnum;

use App\Models\StudentGuardian;
use App\Models\StudentEmergencyContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class GuardianRelationManager extends RelationManager
{
    protected static string $relationship = 'studentGuardians'; 
    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ic_passport_no')
                    ->label('IC/Passport No')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone_hp')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('phone_house')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\TextInput::make('phone_office')
                    ->tel()
                    ->maxLength(12),
                Forms\Components\Select::make('guardian_type')
                    ->required()
                    ->options(GuardianEnum::class),
                Forms\Components\Select::make('nationality')
                    ->label('Nationality')
                    ->options(Country::pluck('name', 'name')->toArray())
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('occupation')
                    ->maxLength(100),
                Forms\Components\TextInput::make('monthly_income')
                    ->numeric(),
                Forms\Components\Checkbox::make('is_emergency_contact')
                    ->label('Is Emergency Contact?'),
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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Convert boolean to enum string
                        $data['is_emergency_contact'] = !empty($data['is_emergency_contact']) ? 'yes' : 'no';
                        return $data;
                    })
                    ->after(function (StudentGuardian $record) {
                        if ($record->is_emergency_contact === 'yes') {
                            StudentEmergencyContact::create([
                                'matric_id'        => $record->matric_id,
                                'relationship'     => $record->guardian_type,
                                'full_name'        => $record->full_name,
                                'address'          => $record->address,
                                'phone_number'     => $record->phone_hp,
                                'alt_phone_number' => $record->phone_house ?? $record->phone_office,
                                'is_primary'       => true,
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_emergency_contact'] = !empty($data['is_emergency_contact']) ? 'yes' : 'no';
                        return $data;
                    })
                    ->after(function (StudentGuardian $record) {
                        if ($record->is_emergency_contact === 'yes') {
                            StudentEmergencyContact::updateOrCreate(
                                [
                                    'matric_id' => $record->matric_id,
                                    'full_name' => $record->full_name,
                                ],
                                [
                                    'relationship'     => $record->guardian_type,
                                    'address'          => $record->address,
                                    'phone_number'     => $record->phone_hp,
                                    'alt_phone_number' => $record->phone_house ?? $record->phone_office,
                                    'is_primary'       => true,
                                ]
                            );
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
