<?php

namespace App\Imports;

use App\Models\Subject;
use App\Enum\SubjectStatusEnum;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SubjectsImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // Matches your header row number
    }
    
    public function collection(Collection $rows)
    {
        Log::info('Starting import, rows count: ' . $rows->count());

        foreach ($rows as $index => $row) {
            Log::info("Processing row {$index}: " . json_encode($row));

            // Skip empty rows
            if (empty($row['subject_code']) && empty($row['subject_name'])) {
                Log::info("Skipping empty row {$index}");
                continue;
            }

            // Clean and validate subject_status
            $rawStatus = $row['subject_status'] ?? '';
            Log::info("Raw subject_status in import: " . $rawStatus);

            $cleanStatus = strtoupper(trim($rawStatus));
            Log::info("Uppercased subject_status in import: " . $cleanStatus);

            $enumStatus = SubjectStatusEnum::tryFrom($cleanStatus);
            
            if (!$enumStatus) {
                Log::warning("Invalid subject_status at row {$index}: '{$rawStatus}', skipping.");
                continue;
            }

            $semester = $row['semester'] ?? '0'; 

            try {
                Subject::create([
                    'subject_code'   => $row['subject_code'],
                    'subject_name'   => $row['subject_name'],
                    'semester'       => $semester,
                    'credit_hour'    => $row['credit_hour'] ?? null,
                    'subject_status' => $enumStatus,
                ]);
                Log::info("Inserted row {$index}");
            } catch (\Exception $e) {
                Log::error("Failed to insert row {$index}: " . $e->getMessage());
            }
        }

        Log::info('Import completed.');
    }
}
