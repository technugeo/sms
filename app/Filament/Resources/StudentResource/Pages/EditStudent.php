<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->record->loadMissing('user');
        $data['email'] = $student->user?->email ?? '';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $student = $this->record;

        // Update related user fields
        if (isset($data['email'])) {
            $student->user->update([
                'email' => $data['email'],
            ]);
        }

        unset($data['email']); // prevent trying to save to students table

        return $data;
    }
}
