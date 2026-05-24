<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Storage;

class ManageSiswas extends ManageRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Siswa'),
            Actions\Action::make('import')
                ->label('Import Data Siswa')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel/CSV')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/csv', 'text/plain'])
                        ->required(),
                    Select::make('kelas_id')
                        ->label('Pilih Kelas')
                        ->options(Kelas::pluck('nama', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = storage_path('app/' . $data['file']);

                    $rows = SimpleExcelReader::create($file)->getRows();
                    $skipped = 0;
                    $imported = 0;

                    $tahunAktif = TahunAjaran::where('apakah_aktif', true)->first();
                    if (!$tahunAktif) {
                        Notification::make()
                            ->title('Gagal: Tidak ada Tahun Ajaran aktif!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $rows->each(function (array $rowProperties) use (&$skipped, &$imported, $data, $tahunAktif) {
                        // Dukungan huruf besar/kecil pada header Excel
                        $row = array_change_key_case($rowProperties, CASE_LOWER);

                        $nisn = $row['nisn'] ?? null;
                        if (!$nisn) return; // Lewati jika tidak ada NISN

                        // Cek duplikat NISN
                        if (Siswa::where('nisn', $nisn)->exists()) {
                            $skipped++;
                            return;
                        }

                        $siswa = Siswa::create([
                            'nisn' => $nisn,
                            'nama' => $row['nama'] ?? '-',
                            'jenis_kelamin' => isset($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : null,
                        ]);

                        $nomorAbsen = $row['nomor_absen'] ?? null;

                        $siswa->kelas()->attach($data['kelas_id'], [
                            'tahun_ajaran_id' => $tahunAktif->id,
                            'nomor_absen' => $nomorAbsen,
                        ]);

                        $imported++;
                    });

                    if ($skipped > 0) {
                        Notification::make()
                            ->title("Berhasil: $imported siswa. Dilewati: $skipped siswa (NISN duplikat).")
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title("Berhasil import $imported siswa")
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
