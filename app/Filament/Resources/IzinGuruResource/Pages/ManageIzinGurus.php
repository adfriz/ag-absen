<?php

namespace App\Filament\Resources\IzinGuruResource\Pages;

use App\Filament\Resources\IzinGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIzinGurus extends ManageRecords
{
    protected static string $resource = IzinGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Izin'),
        ];
    }
}
