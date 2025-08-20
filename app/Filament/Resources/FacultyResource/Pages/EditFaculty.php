<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

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
        $faculty = $this->record;
        $institute = $faculty->institute;

        return [
            route('filament.admin.resources.institutes.index') => 'Institutes',
            $institute
                ? route('filament.admin.resources.institutes.view', $institute)
                : '#' => $institute?->name ?? 'Unknown Institute',
            route('filament.admin.resources.faculties.view', $faculty) => $faculty->name,
            url()->current() => 'Edit',
        ];
    }

}
