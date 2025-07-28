<?php

namespace App\Filament\Resources\SubregionResource\Pages;

use App\Filament\Resources\SubregionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubregion extends EditRecord
{
    protected static string $resource = SubregionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
