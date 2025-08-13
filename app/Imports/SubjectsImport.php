<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectsImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;  // Make sure this matches your header row number
    }
    
   public function collection(Collection $rows)
    {
        Log::info('Starting import, rows count: ' . $rows->count());

        foreach ($rows as $index => $row) {
            Log::info("Processing row {$index}: " . json_encode($row));

            if (empty($row['subject_code']) && empty($row['subject_name'])) {
                Log::info("Skipping empty row {$index}");
                continue;
            }

            
            try {
                $subjectStatus = $row['subject_status'] ?? null;
                Log::info("Raw subject_status in import: " . $subjectStatus);

                $subjectStatusUpper = $subjectStatus ? strtoupper($subjectStatus) : null;
                Log::info("Uppercased subject_status in import: " . $subjectStatusUpper);


                Subject::create([
                    'subject_code'   => $row['subject_code'],
                    'subject_name'   => $row['subject_name'],
                    'semester'       => $row['semester'] ?? null,
                    'credit_hour'    => $row['credit_hour'] ?? null,
                    'subject_status' => 'MPU',
                ]);
                Log::info("Inserted row {$index}");
            } catch (\Exception $e) {
                Log::error("Failed to insert row {$index}: " . $e->getMessage());
            }
        }
        Log::info('Import completed.');
    }
}
