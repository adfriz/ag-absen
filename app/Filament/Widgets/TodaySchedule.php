<?php

namespace App\Filament\Widgets;

use App\Models\Jadwal;
use App\Models\SubstitusiJadwal;
use App\Models\IzinGuru;
use App\Models\TahunAjaran;
use App\Models\HariLibur;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Carbon\Carbon;

class TodaySchedule extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->role === 'guru';
    }

    protected static string $view = 'filament.widgets.today-schedule';

    public string $selectedDate;

    // Cached values — computed once, used everywhere
    protected string $todayDate;
    protected string $sekarangTime;
    protected string $hariTerpilih;
    protected bool $isLibur;
    protected bool $isPast;
    protected bool $isFuture;
    protected bool $isToday;

    public function mount()
    {
        $this->selectedDate = Carbon::today()->toDateString();
        $this->computeCache();
    }

    #[On('date-selected')]
    public function updateSelectedDate(string $date)
    {
        $this->selectedDate = $date;
        $this->resetTable();
    }

    protected function computeCache(): void
    {
        $this->todayDate = Carbon::today()->toDateString();
        $this->sekarangTime = Carbon::now()->toTimeString();
        $selected = Carbon::parse($this->selectedDate);
        $this->hariTerpilih = $selected->locale('id')->isoFormat('dddd');
        $this->isLibur = HariLibur::where('tanggal', $this->selectedDate)->exists();
        $this->isPast = $this->selectedDate < $this->todayDate;
        $this->isFuture = $this->selectedDate > $this->todayDate;
        $this->isToday = $this->selectedDate === $this->todayDate;
    }

    public function getHeading(): string
    {
        $formatted = Carbon::parse($this->selectedDate)->locale('id')->isoFormat('dddd, d MMMM YYYY');
        return "Jadwal Mengajar: {$formatted}";
    }

    public function getDescription(): ?string
    {
        $this->computeCache();

        if ($this->isLibur) {
            $libur = HariLibur::where('tanggal', $this->selectedDate)->first();
            return "HARI LIBUR NASIONAL/SEKOLAH: {$libur->nama} ({$libur->deskripsi})";
        }

        if ($this->isToday) {
            return 'Daftar jam mengajar Anda hari ini. Tombol masuk kelas hanya aktif jika jam pelajaran sudah dimulai.';
        } elseif ($this->isFuture) {
            return 'Jadwal mengajar di masa mendatang. Pengisian presensi belum dibuka.';
        } else {
            return 'Riwayat jadwal mengajar. Anda dapat meninjau riwayat absensi atau melengkapi surat siswa (batas toleransi H+3).';
        }
    }

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        $this->computeCache();

        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Pre-fetch substitution IDs in one query
        $izinSayaHariIni = IzinGuru::where('user_id', $userId)
            ->where('status', 'Disetujui')
            ->where('tanggal_mulai', '<=', $this->selectedDate)
            ->where('tanggal_selesai', '>=', $this->selectedDate)
            ->exists();

        $substitusiIds = SubstitusiJadwal::where('guru_pengganti_id', $userId)
            ->where('tanggal', $this->selectedDate)
            ->pluck('jadwal_id');

        // Get active tahun ajaran ID directly (single fast query, likely 1 row)
        $activeTahunAjaranId = TahunAjaran::where('apakah_aktif', true)->value('id');

        // Build query with eager loading
        $query = Jadwal::query()
            ->where('hari', $this->hariTerpilih)
            ->where('tahun_ajaran_id', $activeTahunAjaranId)
            ->with([
                'classroom',
                'mataPelajaran',
                'teacher',
                'presensi' => function ($q) {
                    $q->where('tanggal', $this->selectedDate);
                },
                'substitusi' => function ($q) {
                    $q->where('tanggal', $this->selectedDate);
                }
            ]);

        if ($izinSayaHariIni) {
            $query->whereIn('id', $substitusiIds);
        } else {
            $query->where(function ($q) use ($substitusiIds, $userId) {
                $q->where('user_id', $userId)
                    ->orWhereIn('id', $substitusiIds);
            });

            $schedulesSubstitutedToOthers = SubstitusiJadwal::where('tanggal', $this->selectedDate)
                ->where('guru_pengganti_id', '!=', $userId)
                ->pluck('jadwal_id');

            $query->whereNotIn('id', $schedulesSubstitutedToOthers);
        }

        // Cache widget-level state for closures
        $isLibur = $this->isLibur;
        $isPast = $this->isPast;
        $isFuture = $this->isFuture;
        $isToday = $this->isToday;
        $sekarangTime = $this->sekarangTime;
        $selectedDate = $this->selectedDate;
        $todayDate = $this->todayDate;

        return $table
            ->query($query)
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('classroom.nama')
                            ->weight('bold')
                            ->size('lg'),
                        Tables\Columns\TextColumn::make('jam')
                            ->alignment('right')
                            ->weight('bold')
                            ->color('primary')
                            ->state(fn($record) => date('H:i', strtotime($record->jam_mulai)) . ' - ' . date('H:i', strtotime($record->jam_selesai))),
                    ]),

                    Tables\Columns\TextColumn::make('teacher.name')
                        ->icon('heroicon-m-user')
                        ->color('gray')
                        ->state(function ($record) use ($userId, $userName) {
                            $isSubstitute = $record->substitusi
                                ->where('guru_pengganti_id', $userId)
                                ->isNotEmpty();
                            return $isSubstitute
                                ? $userName . ' (Guru Pengganti)'
                                : $record->teacher->name;
                        }),

                    Tables\Columns\TextColumn::make('mataPelajaran.nama')
                        ->icon('heroicon-m-book-open')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->state(function ($record) use ($isLibur, $isFuture, $isPast) {
                            if ($isLibur) return 'Hari Libur';
                            if ($isFuture) return 'Akan Datang';

                            $presensiList = $record->presensi;
                            if ($presensiList->isNotEmpty()) {
                                $h = $presensiList->where('status', 'H')->count();
                                $s = $presensiList->where('status', 'S')->count();
                                $i = $presensiList->where('status', 'I')->count();
                                $a = $presensiList->where('status', 'A')->count();
                                return "Hadir: {$h} | Sakit: {$s} | Izin: {$i} | Alpa: {$a}";
                            }

                            return $isPast ? 'Tidak Diisi' : 'Belum Diisi';
                        })
                        ->color(function ($record) use ($isLibur, $isFuture, $isPast) {
                            if ($isLibur) return 'danger';
                            if ($isFuture) return 'gray';
                            if ($record->presensi->isNotEmpty()) return 'success';
                            return $isPast ? 'danger' : 'warning';
                        }),
                ])->space(3),
            ])
            ->actions([
                Tables\Actions\Action::make('masuk_kelas')
                    ->label(function ($record) use ($isLibur, $isFuture, $isPast, $sekarangTime, $selectedDate, $todayDate) {
                        if ($isLibur) return 'Hari Libur';
                        if ($isFuture) return 'Belum Dimulai';

                        $sudahDiabsen = $record->presensi->isNotEmpty();

                        if ($isPast) {
                            $selisihHari = Carbon::parse($todayDate)->diffInDays(Carbon::parse($selectedDate), false);
                            if ($selisihHari >= -3) {
                                return $sudahDiabsen ? 'Koreksi Absen (H+3)' : 'Isi Absen Susulan';
                            }
                            return 'Lihat Riwayat';
                        }

                        if ($sekarangTime < $record->jam_mulai) return 'Belum Waktunya';
                        return $sudahDiabsen ? 'Lihat / Edit Absensi' : 'Masuk Kelas';
                    })
                    ->color(function ($record) use ($isLibur, $isFuture, $isPast, $sekarangTime, $selectedDate, $todayDate) {
                        if ($isLibur || $isFuture) return 'gray';

                        $sudahDiabsen = $record->presensi->isNotEmpty();

                        if ($isPast) {
                            $selisihHari = Carbon::parse($todayDate)->diffInDays(Carbon::parse($selectedDate), false);
                            return ($selisihHari >= -3) ? 'warning' : 'info';
                        }

                        if ($sekarangTime < $record->jam_mulai) return 'gray';
                        return $sudahDiabsen ? 'info' : 'success';
                    })
                    ->url(function ($record) use ($selectedDate) {
                        return route('filament.app.pages.attendance.{jadwal}', ['jadwal' => $record->id, 'tanggal' => $selectedDate]);
                    })
                    ->disabled(function ($record) use ($isLibur, $isFuture, $isToday, $sekarangTime) {
                        if ($isLibur || $isFuture) return true;
                        if ($isToday && $sekarangTime < $record->jam_mulai) return true;
                        return false;
                    })
                    ->icon(fn($record) => $record->presensi->isNotEmpty() ? 'heroicon-m-pencil-square' : 'heroicon-m-play')
                    ->button()
            ]);
    }
}
