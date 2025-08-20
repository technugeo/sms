<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFaculty extends ViewRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            FacultyResource\RelationManagers\CoursesRelationManager::class,
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
            url()->current() => $faculty->name,
        ];
    }

}
