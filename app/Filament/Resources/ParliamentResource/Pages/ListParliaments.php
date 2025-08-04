<?php

namespace App\Filament\Resources\ParliamentResource\Pages;

use App\Filament\Resources\ParliamentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParliaments extends ListRecords
{
    protected static string $resource = ParliamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
