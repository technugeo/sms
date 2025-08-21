<?php

namespace App\Imports;

use App\Models\Institute;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class InstitutesImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // header row
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting institute import, rows count: ' . $rows->count());

        foreach ($rows as $index => $row) {
            Log::info("Processing row {$index}: " . json_encode($row));

            // Skip empty rows
            if (empty($row['mqa_institute_id']) && empty($row['name'])) {
                Log::info("Skipping empty row {$index}");
                continue;
            }

            try {
                // Check if institute already exists by mqa_institute_id
                $exists = Institute::where('mqa_institute_id', $row['mqa_institute_id'])->exists();
                
                if ($exists) {
                    Log::info("Skipping row {$index} because mqa_institute_id '{$row['mqa_institute_id']}' already exists.");
                    continue;
                }

                // Create new institute
                $institute = new Institute();
                $institute->mqa_institute_id = $row['mqa_institute_id'];
                $institute->name = $row['name'] ?? null;
                $institute->abbreviation = $row['abbreviation'] ?? null;
                $institute->country = $row['country'] ?? null;

                // Optional: handle other fields later if needed
                // $institute->state = $row['state'] ?? null;
                // $institute->district = $row['district'] ?? null;
                // $institute->parliament = $row['parliament'] ?? null;
                // $institute->dun = $row['dun'] ?? null;

                $institute->save();

                Log::info("Inserted new institute: {$institute->name}");
            } catch (\Exception $e) {
                Log::error("Failed to insert row {$index}: " . $e->getMessage());
            }
        }

        Log::info('Institute import completed.');
    }
}
