<?php

namespace App\Filament\Resources\HariLiburResource\Pages;

use App\Filament\Resources\HariLiburResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageHariLiburs extends ManageRecords
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Hari Libur')
                ->using(function (array $data, string $model): Model {
                    $start = \Carbon\Carbon::parse($data['tanggal_mulai']);
                    $end = isset($data['tanggal_selesai']) ? \Carbon\Carbon::parse($data['tanggal_selesai']) : $start;

                    if ($end->lt($start)) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }

                    $createdRecord = null;
                    $current = $start->copy();
                    while ($current->lte($end)) {
                        $record = $model::updateOrCreate(
                            ['tanggal' => $current->format('Y-m-d')],
                            [
                                'nama' => $data['nama'],
                                'deskripsi' => $data['deskripsi'] ?? null,
                            ]
                        );
                        if (!$createdRecord) {
                            $createdRecord = $record;
                        }
                        $current->addDay();
                    }

                    return $createdRecord;
                }),
        ];
    }
}
