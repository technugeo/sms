<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $department = $this->record;
        $institute = $department->institute;

        return [
            route('filament.admin.resources.institutes.index') => 'Institutes',
            route('filament.admin.resources.institutes.edit', $institute) => $institute->name,
            url()->current() => $department->name,
        ];
    }


}
