<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ForeignAddressRelationManager extends RelationManager
{
    protected static string $relationship = 'foreignAddress';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return strtolower($ownerRecord->citizen->value) === 'foreign';
    }



    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('address_1')->required(),
            Forms\Components\TextInput::make('address_2')->required(),
            Forms\Components\TextInput::make('postcode')->required(),
            Forms\Components\Select::make('country_id')
                ->label('Country')
                ->options(Country::pluck('name', 'id')->toArray())
                ->searchable()
                ->preload()
                ->reactive(),

            Forms\Components\Select::make('state_id')
                ->label('State')
                ->options(function (callable $get) {
                    $countryId = $get('country_id');
                    return $countryId
                        ? State::where('country_id', $countryId)->pluck('name', 'id')->toArray()
                        : [];
                })
                ->searchable()
                ->preload()
                ->reactive()
                ->disabled(fn (callable $get) => !$get('country_id')),

            Forms\Components\Select::make('city_id')
                ->label('City')
                ->options(function (callable $get) {
                    $stateId = $get('state_id');
                    return $stateId
                        ? City::where('state_id', $stateId)->pluck('name', 'id')->toArray()
                        : [];
                })
                ->searchable()
                ->preload()
                ->reactive()
                ->disabled(fn (callable $get) => !$get('state_id')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('address_1')->searchable(),
                Tables\Columns\TextColumn::make('address_2')->searchable(),
                Tables\Columns\TextColumn::make('postcode'),
                Tables\Columns\TextColumn::make('country.name'),
                Tables\Columns\TextColumn::make('state.name'),
                Tables\Columns\TextColumn::make('city.name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire) {
                        $data['user_id'] = $livewire->getOwnerRecord()->user_id;
                        $data['address_type'] = 'Foreign';
                        return $data;
                    })
                    ->after(function ($record, RelationManager $livewire) {
                        $student = $livewire->getOwnerRecord();
                        $student->update([
                            'address_id' => $record->id,
                        ]);                  
                    })

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->after(function ($record, RelationManager $livewire) {
                    $student = $livewire->getOwnerRecord();
                    if ($student->address_id !== $record->id) {
                        $student->update([
                            'address_id' => $record->id,
                        ]); 
                    }
                }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
