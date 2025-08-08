<?php

namespace App\Filament\Resources\InstituteResource\Pages;

use App\Filament\Resources\InstituteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitute extends ViewRecord
{
    protected static string $resource = InstituteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function getBreadcrumbs(): array
    {
        $institute = $this->record;

        return [
            route('filament.admin.resources.institutes.index') => 'Institutes',
            url()->current() => $institute->name,
        ];
    }
}
