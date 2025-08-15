<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Pre-fill the form with user name and email.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        $data['name'] = $user?->name ?? '';
        $data['email'] = $user?->email ?? '';

        return $data;
    }

    /**
     * Save changes to user and staff.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = $record->user;

        // Update the related user (name & email)
        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // Update role if provided
        if (!empty($data['access_level'])) {
            // Remove existing roles and assign new role
            $user->syncRoles([$data['access_level']]);
        }

        // Remove user-specific fields before updating the staff
        unset($data['name'], $data['email'], $data['access_level']);

        // Update the staff record
        $record->update($data);

        return $record;
    }

}
