<?php

namespace App\Filament\Resources\StudentGuardianResource\Pages;

use App\Filament\Resources\StudentGuardianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentGuardian extends EditRecord
{
    protected static string $resource = StudentGuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
