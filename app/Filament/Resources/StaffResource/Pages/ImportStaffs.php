<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StaffsImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportStaffs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StaffResource::class;
    protected static string $view = 'filament.resources.staff-resource.pages.import-staffs';
    protected static ?string $title = 'Import Staffs';

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
                    ->directory('staff_uploads') 
                    ->preserveFilenames(false)     
                    ->maxFiles(1)
                    ->saveUploadedFileUsing(function ($file) {
                        $timestamp = now()->format('Ymd_His');
                        $extension = $file->getClientOriginalExtension() ?: 'csv';
                        $filename = "staff_{$timestamp}.{$extension}";
                        return $file->storeAs('staff_uploads', $filename);
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

        // Always convert uploaded file to CSV before importing
        $csvFilename = 'staff_uploads/staff_' . Str::uuid() . '.csv';
        $csvPath = Storage::path($csvFilename);

        try {
            // Convert Excel or CSV → CSV file
            Excel::store(new \App\Imports\StaffsImport, $csvFilename, null, \Maatwebsite\Excel\Excel::CSV);

            // Now import from the normalized CSV file
            Excel::import(new StaffsImport(), $csvPath, null, \Maatwebsite\Excel\Excel::CSV);

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return;
        }

        // Clean up files
        Storage::delete($file);
        Storage::delete($csvFilename);

        Notification::make()
            ->title('Staffs imported successfully!')
            ->success()
            ->send();

        $this->redirect(StaffResource::getUrl('index'));
    }
}
