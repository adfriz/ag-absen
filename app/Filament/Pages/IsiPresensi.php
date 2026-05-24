<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\SubstitusiJadwal;
use App\Models\HariLibur;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class IsiPresensi extends Page
{
    use WithFileUploads;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'guru';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.isi-presensi';

    // Sembunyikan dari sidebar karena diakses lewat link widget
    protected static bool $shouldRegisterNavigation = false;

    // Define custom route dengan parameter jadwal
    protected static ?string $slug = 'attendance/{jadwal}';

    public Jadwal $jadwal;
    public string $tanggal;
    public $siswaData = []; // [siswa_id => [status, catatan, menit_terlambat, bukti_surat, bukti_surat_existing, is_locked]]
    public $buktiSuratUpload = []; // [siswa_id => file]
    
    public $hariIniLibur = false;
    public $namaLibur = '';
    public $belumWaktunya = false;
    public $isSubstitute = false;
    public $savedMessage = '';

    public function mount(Jadwal $jadwal): void
    {
        $this->jadwal = $jadwal;
        $this->tanggal = request()->query('tanggal', Carbon::today()->toDateString());

        // 1. Validasi Hak Akses (Guru asli ATAU Guru Pengganti pada tanggal target)
        $isOriginalTeacher = $jadwal->user_id === Auth::id();
        $this->isSubstitute = SubstitusiJadwal::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $this->tanggal)
            ->where('guru_pengganti_id', Auth::id())
            ->exists();

        if (!$isOriginalTeacher && !$this->isSubstitute) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengabsen kelas ini.');
        }

        // 2. Validasi Hari Libur
        $libur = HariLibur::where('tanggal', $this->tanggal)->first();
        if ($libur) {
            $this->hariIniLibur = true;
            $this->namaLibur = $libur->nama;
        }

        // 3. Validasi Jam Mulai Pelajaran (Hanya jika tanggal target adalah hari ini)
        if ($this->tanggal === Carbon::today()->toDateString()) {
            $sekarangTime = Carbon::now()->format('H:i:s');
            if ($sekarangTime < $jadwal->jam_mulai) {
                $this->belumWaktunya = true;
            }
        }

        // 4. Ambil Siswa Aktif di Kelas pada Tahun Ajaran ini
        $students = Siswa::whereHas('kelas', function($q) {
            $q->where('kelas_siswa.kelas_id', $this->jadwal->kelas_id)
              ->where('kelas_siswa.tahun_ajaran_id', $this->jadwal->tahun_ajaran_id);
        })->orderBy('nama')->get();

        // 5. Load data presensi jika sudah pernah diisi
        foreach ($students as $siswa) {
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->where('jadwal_id', $this->jadwal->id)
                ->where('tanggal', $this->tanggal)
                ->first();

            $status = $presensi ? $presensi->status : 'H';
            $catatan = $presensi ? $presensi->catatan : '';
            $menitTerlambat = $presensi ? $presensi->menit_terlambat : 0;
            $buktiSuratExisting = $presensi ? $presensi->bukti_surat : null;

            // Logika Kunci H+3 untuk edit presensi
            $isLocked = false;
            
            if ($presensi) {
                $selisihHari = Carbon::today()->diffInDays(Carbon::parse($presensi->tanggal), false);
                
                if ($selisihHari > 0) { // Jika dibuka di hari berikutnya (H+1 atau lebih)
                    if ($selisihHari <= 3) {
                        // Toleransi H+3: Hanya boleh mengubah status Alpa (A), Sakit (S), atau Izin (I) untuk melengkapi bukti surat
                        if (!in_array($presensi->status, ['A', 'S', 'I'])) {
                            $isLocked = true;
                        }
                    } else {
                        // Melebihi H+3: Terkunci total
                        $isLocked = true;
                    }
                }
            }

            $this->siswaData[$siswa->id] = [
                'siswa_id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'status' => $status,
                'catatan' => $catatan,
                'menit_terlambat' => $menitTerlambat,
                'bukti_surat_existing' => $buktiSuratExisting,
                'is_locked' => $isLocked
            ];
        }
    }

    public function getTitle(): string
    {
        return "Presensi Kelas " . $this->jadwal->classroom->nama;
    }

    public function getSubheading(): ?string
    {
        $formattedDate = Carbon::parse($this->tanggal)->locale('id')->isoFormat('dddd, d MMMM YYYY');
        $roleInfo = $this->isSubstitute ? " (Guru Pengganti)" : "";
        return "Mata Pelajaran: " . $this->jadwal->mataPelajaran->nama . 
            " | Jam: " . date('H:i', strtotime($this->jadwal->jam_mulai)) . " - " . date('H:i', strtotime($this->jadwal->jam_selesai)) . 
            " | Tanggal: " . $formattedDate . $roleInfo;
    }

    public function updatedSiswaData(mixed $value, string $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'status') {
            $siswaId = $parts[0];
            $newStatus = $value;

            // Reset conditional inputs jika status berubah
            if ($newStatus !== 'D') {
                $this->siswaData[$siswaId]['catatan'] = '';
            }
            if ($newStatus !== 'T') {
                $this->siswaData[$siswaId]['menit_terlambat'] = 0;
            }
        }
    }

    public function simpanPresensi()
    {
        if ($this->hariIniLibur) {
            session()->flash('error', 'Tidak dapat menyimpan absensi pada hari libur.');
            return;
        }

        if ($this->belumWaktunya) {
            session()->flash('error', 'Waktu pelajaran belum dimulai.');
            return;
        }

        foreach ($this->siswaData as $siswaId => $data) {
            if ($data['is_locked']) {
                continue;
            }

            if ($data['status'] === 'D' && empty($data['catatan'])) {
                session()->flash('error', "Siswa {$data['nama']} berstatus Dispensasi, Keterangan wajib diisi!");
                return;
            }

            if ($data['status'] === 'T' && (empty($data['menit_terlambat']) || $data['menit_terlambat'] <= 0)) {
                session()->flash('error', "Siswa {$data['nama']} berstatus Terlambat, Durasi menit wajib diisi!");
                return;
            }

            // Upload bukti surat
            $buktiSuratPath = $data['bukti_surat_existing'];
            if (isset($this->buktiSuratUpload[$siswaId])) {
                $file = $this->buktiSuratUpload[$siswaId];
                $buktiSuratPath = $file->store('bukti_surat_presensi', 'public');
            }

            if (!in_array($data['status'], ['S', 'I'])) {
                $buktiSuratPath = null;
            }

            Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $this->jadwal->id,
                    'tanggal' => $this->tanggal,
                ],
                [
                    'status' => $data['status'],
                    'catatan' => $data['status'] === 'D' ? $data['catatan'] : null,
                    'menit_terlambat' => $data['status'] === 'T' ? $data['menit_terlambat'] : 0,
                    'bukti_surat' => $buktiSuratPath,
                    'diabsen_oleh' => Auth::id(),
                ]
            );
        }

        Notification::make()
            ->title('Presensi Berhasil Disimpan')
            ->body('Data kehadiran kelas ' . $this->jadwal->classroom->nama . ' telah tersimpan.')
            ->success()
            ->send();

        return redirect('/dashboard');
    }
}
