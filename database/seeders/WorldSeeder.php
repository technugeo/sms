<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WorldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting location data seeder...');

        // List of tables to truncate (in reverse FK dependency order)
        $tables = [
            'cities',
            'states',
            'countries',
            'subregions',
            'regions',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("✅ Truncated: $table");
        }

        // List of SQL files (ordered by hierarchy)
        $sqlFiles = [
            'regions.sql',
            'subregions.sql',
            'countries.sql',
            'states.sql',
            'cities.sql',
            'parliament_dun.sql',
        ];

        foreach ($sqlFiles as $file) {
            $path = database_path("seeders/sql/{$file}");

            if (!File::exists($path)) {
                $this->command->warn("⚠️ Skipped missing file: {$file}");
                continue;
            }

            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info("✅ Imported: {$file}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🎉 Location seeding complete.');
    }
}
