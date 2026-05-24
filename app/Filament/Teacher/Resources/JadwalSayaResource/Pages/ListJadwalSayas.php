<?php

namespace App\Filament\Teacher\Resources\JadwalSayaResource\Pages;

use App\Filament\Teacher\Resources\JadwalSayaResource;
use Filament\Resources\Pages\ListRecords;

class ListJadwalSayas extends ListRecords
{
    protected static string $resource = JadwalSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
