<?php

namespace App\Filament\Resources\StaffResource\RelationManagers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses'; // must match method in Student model

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
                ->options(fn (callable $get) => 
                    $get('country_id') 
                        ? State::where('country_id', $get('country_id'))->pluck('name', 'id')->toArray() 
                        : [])
                ->searchable()
                ->preload()
                ->reactive()
                ->disabled(fn (callable $get) => !$get('country_id')),

            Forms\Components\Select::make('city_id')
                ->label('City')
                ->options(fn (callable $get) => 
                    $get('state_id') 
                        ? City::where('state_id', $get('state_id'))->pluck('name', 'id')->toArray() 
                        : [])
                ->searchable()
                ->preload()
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
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
