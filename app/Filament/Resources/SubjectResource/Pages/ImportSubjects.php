<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Imports\SubjectsImport;
use App\Filament\Resources\SubjectResource;

use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportSubjects extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SubjectResource::class;
    protected static string $view = 'filament.resources.subject-resource.pages.import-subjects';
    protected static ?string $title = 'Import Subjects';

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
                FileUpload::make('upload_file')
                    ->label('Excel or CSV File')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->directory('subject_uploads') 
                    ->preserveFilenames(false)     
                    ->maxFiles(1)
                    ->saveUploadedFileUsing(function ($file) {
                        $timestamp = now()->format('Ymd_His');
                        $extension = $file->getClientOriginalExtension() ?: 'csv';
                        $filename = "subject_{$timestamp}.{$extension}";
                        return $file->storeAs('subject_uploads', $filename);
                    }),
            ])
            ->statePath('formData');
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        $fileValue = $state['upload_file'] ?? null;
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
            // Maatwebsite Excel auto-detects format (Excel or CSV)
            Excel::import(new SubjectsImport(), $path);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        // Delete uploaded file after import
        Storage::delete($file);

        Notification::make()
            ->title('Subjects imported successfully!')
            ->success()
            ->send();

        $this->redirect(SubjectResource::getUrl('index'));
    }
}
