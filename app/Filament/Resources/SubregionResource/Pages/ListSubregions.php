<?php

namespace App\Filament\Resources\SubregionResource\Pages;

use App\Filament\Resources\SubregionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubregions extends ListRecords
{
    protected static string $resource = SubregionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
