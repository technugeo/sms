<?php

namespace App\Filament\Resources\InstituteResource\Pages;

use App\Filament\Resources\InstituteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Auth;

class CreateInstitute extends CreateRecord
{
    protected static string $resource = InstituteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = Auth::id();

        if (!$userId) {
            throw new \Exception('No logged-in user found. Cannot set created_by.');
        }

        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        return $data;
    }

}
