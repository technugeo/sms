<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

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

        $breadcrumbs[route('filament.admin.resources.courses.view', $course)] = $course->prog_name ?? 'View Course';
        $breadcrumbs[url()->current()] = 'Edit';

        return $breadcrumbs;
    }



}
