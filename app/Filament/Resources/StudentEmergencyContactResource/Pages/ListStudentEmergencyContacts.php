<?php

namespace App\Filament\Resources\StudentEmergencyContactResource\Pages;

use App\Filament\Resources\StudentEmergencyContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentEmergencyContacts extends ListRecords
{
    protected static string $resource = StudentEmergencyContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
