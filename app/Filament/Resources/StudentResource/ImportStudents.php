<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;

use App\Models\Student;
use App\Filament\Resources\StudentResource;

class ImportStudents extends Page
{
    use WithFileUploads;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.import-students';

    public $excel_file;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('excel_file')
                ->label('Excel File (.xlsx)')
                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                ->required(),
        ];
    }

    public function submit()
    {
        $this->validate([
            'excel_file' => 'required|file|mimes:xlsx',
        ]);

        $path = $this->excel_file->store('temp');

        $data = Excel::toCollection(null, storage_path('app/' . $path));

        if ($data->isEmpty() || !$data[0]->count()) {
            Notification::make()
                ->title('No data found in Excel file')
                ->danger()
                ->send();

            return;
        }

        $rows = $data[0];

        foreach ($rows as $row) {
            Student::create([
                'full_name' => $row['full_name'] ?? '',
                'matric_id' => $row['matric_id'] ?? '',
                'email' => $row['email'] ?? '',
                'nric' => $row['nric'] ?? '',
                'passport_no' => $row['passport_no'] ?? '',
                'citizen' => $row['citizen'] ?? '',
                'nationality_type' => $row['nationality_type'] ?? '',
                'nationality' => $row['nationality'] ?? '',
                'marriage_status' => $row['marriage_status'] ?? '',
                'academic_status' => $row['academic_status'] ?? '',
            ]);
        }

        Notification::make()
            ->title('Students imported successfully!')
            ->success()
            ->send();

        return redirect(StudentResource::getUrl());
    }
}
