<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Filament\Resources\StaffResource\RelationManagers\AddressRelationManager;

use App\Models\Staff;
use App\Models\Designation;
use App\Enum\CitizenEnum;
use App\Enum\MarriageEnum;
use App\Enum\GenderEnum;
use App\Enum\NationalityEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nric')
                    ->required()
                    ->maxLength(12),

                
                Forms\Components\Select::make('nationality_type')
                    ->required()
                    ->options(NationalityEnum::class),

                Forms\Components\Select::make('citizen')
                    ->required()
                    ->options(CitizenEnum::class),
                // Forms\Components\TextInput::make('citizen')
                //     ->required(),
                // Forms\Components\TextInput::make('marriage_status')
                //     ->required(),
                Forms\Components\Select::make('marriage_status')
                    ->required()
                    ->options(MarriageEnum::class),
                // Forms\Components\TextInput::make('gender')
                //     ->required(),
                Forms\Components\Select::make('gender')
                    ->required()
                    ->options(GenderEnum::class),
                // Forms\Components\TextInput::make('address_id')
                //     ->required()
                //     ->numeric(),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('designation_id')
                    ->label('Designation')
                    ->relationship(
                        name: 'designation',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, $get) => $query->where('department_id', $get('department_id'))
                    )
                    ->required()
                    ->disabled(fn (callable $get) => !$get('department_id'))
                    ->reactive(),

            ]);

    }

    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nric')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality_type'),
                Tables\Columns\TextColumn::make('citizen'),
                Tables\Columns\TextColumn::make('marriage_status'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('address_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designation.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
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
            RelationManagers\AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
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
