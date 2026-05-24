<?php

namespace App\Filament\Resources\JadwalSayaResource\Pages;

use App\Filament\Resources\JadwalSayaResource;
use Filament\Resources\Pages\ListRecords;

class ListJadwalSayas extends ListRecords
{
    protected static string $resource = JadwalSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
