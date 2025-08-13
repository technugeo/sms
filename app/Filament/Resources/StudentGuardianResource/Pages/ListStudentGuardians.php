<?php

namespace App\Filament\Resources\StudentGuardianResource\Pages;

use App\Filament\Resources\StudentGuardianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentGuardians extends ListRecords
{
    protected static string $resource = StudentGuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
