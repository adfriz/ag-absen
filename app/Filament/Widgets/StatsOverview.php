<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa', Siswa::count())
                ->description('Jumlah seluruh siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Total Kelas', Kelas::count())
                ->description('Jumlah kelas aktif')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success'),
            Stat::make('Kehadiran Hari Ini', Presensi::where('tanggal', now()->toDateString())->where('status', 'hadir')->count())
                ->description('Siswa yang sudah hadir hari ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
