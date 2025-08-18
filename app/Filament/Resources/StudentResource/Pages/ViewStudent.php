<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\EditAction; // <-- correct import for v3

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'View Profile';
    }
}
