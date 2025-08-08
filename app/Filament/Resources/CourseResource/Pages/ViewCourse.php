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
        $department = $course->department;
        $institute = $department?->institute;

        return [
            route('filament.admin.resources.institutes.index') => 'Institutes',

            // Institute
            $institute
                ? route('filament.admin.resources.institutes.view', $institute)
                : '#' => $institute?->name ?? 'Unknown Institute',

            // Department
            $department
                ? route('filament.admin.resources.departments.view', $department)
                : '#' => $department?->name ?? 'Unknown Department',

            // Course (prog_name from lib_course_prog)
            url()->current() => $course->prog_name ?? 'View Course',
        ];
    }



}
