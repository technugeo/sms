<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Storage;

class ImportStudents extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.resources.student-resource.pages.import-students';
    protected static ?string $title = 'Import Students';

    // Store all form state here (safe for Livewire)
    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('excel_file')
                    ->label('Excel File')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->directory('student_uploads') // Store in storage/app/student_uploads
                    ->preserveFilenames(false)     // Let us rename the file below
                    ->maxFiles(1)
                    ->saveUploadedFileUsing(function ($file) {
                        $timestamp = now()->format('Ymd_His');
                        $extension = $file->getClientOriginalExtension() ?: 'xlsx';
                        $filename = "student_{$timestamp}.{$extension}";
                        return $file->storeAs('student_uploads', $filename);
                    }),
            ])
            ->statePath('formData'); // bind entire form to $formData
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        $fileValue = $state['excel_file'] ?? null;

        // Normalize the file value to a string path
        $file = is_array($fileValue) ? ($fileValue[0] ?? null) : $fileValue;

        if (! $file) {
            Notification::make()
                ->title('Please upload a file.')
                ->danger()
                ->send();
            return;
        }

        if (! Storage::exists($file)) {
            Notification::make()
                ->title('Uploaded file does not exist on disk.')
                ->danger()
                ->send();
            return;
        }

        $path = Storage::path($file);

        try {
            Excel::import(new StudentsImport(), $path);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        // Optionally delete file after import
        Storage::delete($file);

        Notification::make()
            ->title('Students imported successfully!')
            ->success()
            ->send();

        $this->redirect(StudentResource::getUrl('index'));
    }
}
