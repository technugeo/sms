<?php

namespace App\Filament\Resources\InstituteResource\Pages;

use App\Filament\Resources\InstituteResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InstitutesImport;
use Illuminate\Support\Facades\Storage;

class ImportInstitutes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = InstituteResource::class;
    protected static string $view = 'filament.resources.institute-resource.pages.import-institutes';
    protected static ?string $title = 'Import Institutes';

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
                    ->directory('institute_uploads') 
                    ->preserveFilenames(false)     
                    ->maxFiles(1)
                    ->saveUploadedFileUsing(function ($file) {
                        $timestamp = now()->format('Ymd_His');
                        $extension = $file->getClientOriginalExtension() ?: 'csv';
                        $filename = "institute_{$timestamp}.{$extension}";
                        return $file->storeAs('institute_uploads', $filename);
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
            Excel::import(new InstitutesImport(), $path);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        Storage::delete($file);

        Notification::make()
            ->title('Institutes imported successfully!')
            ->success()
            ->send();

        $this->redirect(InstituteResource::getUrl('index'));
    }
}
