<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function getBreadcrumbs(): array
    {
        $course = $this->record; 
        $faculty = $course->faculty;
        $institute = $faculty?->institute;

        $breadcrumbs = [
            route('filament.admin.resources.institutes.index') => 'Institutes',
        ];

        if ($institute) {
            $breadcrumbs[route('filament.admin.resources.institutes.view', $institute)] = $institute->name;
        }

        if ($faculty) {
            $breadcrumbs[route('filament.admin.resources.faculties.view', $faculty)] = $faculty->name;
        }

        $breadcrumbs[url()->current()] = $course->prog_name ?? 'View Course';

        return $breadcrumbs;
    }




}
