<?php

namespace App\Filament\Resources\InstituteResource\Pages;

use App\Filament\Resources\InstituteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstitute extends EditRecord
{
    protected static string $resource = InstituteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    public function getBreadcrumbs(): array
    {
        $institute = $this->record;

        return [
            route('filament.admin.resources.institutes.index') => 'Institutes',
            route('filament.admin.resources.institutes.view', $institute) => $institute->name,
            url()->current() => 'Edit',
        ];
    }





}
