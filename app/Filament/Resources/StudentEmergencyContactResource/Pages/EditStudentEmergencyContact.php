<?php

namespace App\Filament\Resources\StudentEmergencyContactResource\Pages;

use App\Filament\Resources\StudentEmergencyContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentEmergencyContact extends EditRecord
{
    protected static string $resource = StudentEmergencyContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
