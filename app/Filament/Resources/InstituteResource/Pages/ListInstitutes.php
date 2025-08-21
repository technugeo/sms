<?php

namespace App\Filament\Resources\InstituteResource\Pages;

use App\Filament\Resources\InstituteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstitutes extends ListRecords
{
    protected static string $resource = InstituteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Upload Excel')
                ->url(ImportInstitutes::getUrl())
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
