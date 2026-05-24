<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\HariLibur;
use App\Models\IzinGuru;
use App\Models\SubstitusiJadwal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. GURU & ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@alghazaly.com'],
            [
                'name' => 'Administrator Sekolah',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        $ahmad = User::updateOrCreate(
            ['email' => 'ahmad@alghazaly.com'],
            [
                'name' => 'Ahmad S.Pd. (Guru Matematika)',
                'username' => 'ahmad',
                'password' => Hash::make('password'),
                'role' => 'guru'
            ]
        );

        $budi = User::updateOrCreate(
            ['email' => 'budi@alghazaly.com'],
            [
                'name' => 'Budi S.Pd. (Guru Fisika & Cadangan)',
                'username' => 'budi',
                'password' => Hash::make('password'),
                'role' => 'guru'
            ]
        );

        $citra = User::updateOrCreate(
            ['email' => 'citra@alghazaly.com'],
            [
                'name' => 'Citra S.Pd. (Guru Kimia)',
                'username' => 'citra',
                'password' => Hash::make('password'),
                'role' => 'guru'
            ]
        );

        // 2. TAHUN AJARAN
        $tahunAktif = TahunAjaran::updateOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
            ['apakah_aktif' => true]
        );

        $tahunPasif = TahunAjaran::updateOrCreate(
            ['tahun' => '2025/2026', 'semester' => 'Genap'],
            ['apakah_aktif' => false]
        );

        // 3. MATA PELAJARAN
        $mtk = MataPelajaran::updateOrCreate(
            ['kode' => 'MTK-10'],
            ['nama' => 'Matematika Peminatan']
        );

        $fis = MataPelajaran::updateOrCreate(
            ['kode' => 'FIS-10'],
            ['nama' => 'Fisika Dasar']
        );

        $kim = MataPelajaran::updateOrCreate(
            ['kode' => 'KIM-10'],
            ['nama' => 'Kimia Organik']
        );

        $ind = MataPelajaran::updateOrCreate(
            ['kode' => 'BIN-10'],
            ['nama' => 'Bahasa Indonesia']
        );

        // 4. KELAS
        $x_ipa1 = Kelas::updateOrCreate(
            ['nama' => 'X IPA 1'],
            ['tingkat' => '10']
        );

        $x_ipa2 = Kelas::updateOrCreate(
            ['nama' => 'X IPA 2'],
            ['tingkat' => '10']
        );

        $xi_ipa1 = Kelas::updateOrCreate(
            ['nama' => 'XI IPA 1'],
            ['tingkat' => '11']
        );

        // 5. SISWA & PIVOT KELAS
        $siswaList = [
            ['nisn' => '0012345601', 'nama' => 'Aditya Saputra'],
            ['nisn' => '0012345602', 'nama' => 'Beni Hermawan'],
            ['nisn' => '0012345603', 'nama' => 'Cici Amalia'],
            ['nisn' => '0012345604', 'nama' => 'Dian Lestari'],
            ['nisn' => '0012345605', 'nama' => 'Eka Prasetya'],
        ];

        foreach ($siswaList as $s) {
            $siswa = Siswa::updateOrCreate(
                ['nisn' => $s['nisn']],
                ['nama' => $s['nama']]
            );

            // Masukkan ke X IPA 1 untuk Tahun Ajaran aktif
            $siswa->kelas()->syncWithPivotValues([$x_ipa1->id], ['tahun_ajaran_id' => $tahunAktif->id], false);
        }

        // Tambah 2 siswa lagi untuk X IPA 2
        $siswaListIpa2 = [
            ['nisn' => '0012345606', 'nama' => 'Farhan Nugraha'],
            ['nisn' => '0012345607', 'nama' => 'Gita Permata'],
        ];

        foreach ($siswaListIpa2 as $s) {
            $siswa = Siswa::updateOrCreate(
                ['nisn' => $s['nisn']],
                ['nama' => $s['nama']]
            );
            $siswa->kelas()->syncWithPivotValues([$x_ipa2->id], ['tahun_ajaran_id' => $tahunAktif->id], false);
        }

        // 6. JADWAL PELAJARAN
        // Jadwal Senin untuk Guru Ahmad (Matematika di X IPA 1)
        $jadwalAhmad = Jadwal::updateOrCreate(
            [
                'user_id' => $ahmad->id,
                'kelas_id' => $x_ipa1->id,
                'mata_pelajaran_id' => $mtk->id,
                'tahun_ajaran_id' => $tahunAktif->id,
                'hari' => 'Senin',
            ],
            [
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:30:00'
            ]
        );

        // Jadwal Hari Ini (Sabtu) untuk Guru Ahmad (Matematika di X IPA 1) untuk keperluan testing
        $jadwalSabtuAhmad = Jadwal::updateOrCreate(
            [
                'user_id' => $ahmad->id,
                'kelas_id' => $x_ipa1->id,
                'mata_pelajaran_id' => $mtk->id,
                'tahun_ajaran_id' => $tahunAktif->id,
                'hari' => 'Sabtu',
            ],
            [
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '19:00:00'
            ]
        );

        // Bersihkan presensi hari ini jika ada agar bisa dites ulang
        \App\Models\Presensi::where('jadwal_id', $jadwalSabtuAhmad->id)
            ->where('tanggal', '2026-05-23')
            ->delete();

        // Jadwal Senin untuk Guru Budi (Fisika di X IPA 1)
        $jadwalBudi = Jadwal::updateOrCreate(
            [
                'user_id' => $budi->id,
                'kelas_id' => $x_ipa1->id,
                'mata_pelajaran_id' => $fis->id,
                'tahun_ajaran_id' => $tahunAktif->id,
                'hari' => 'Senin',
            ],
            [
                'jam_mulai' => '09:45:00',
                'jam_selesai' => '11:15:00'
            ]
        );

        // Jadwal Senin untuk Guru Citra (Kimia di X IPA 1)
        $jadwalCitra = Jadwal::updateOrCreate(
            [
                'user_id' => $citra->id,
                'kelas_id' => $x_ipa1->id,
                'mata_pelajaran_id' => $kim->id,
                'tahun_ajaran_id' => $tahunAktif->id,
                'hari' => 'Senin',
            ],
            [
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '14:30:00'
            ]
        );

        // 7. HARI LIBUR
        HariLibur::updateOrCreate(
            ['tanggal' => Carbon::now()->addDays(7)->format('Y-m-d')],
            [
                'nama' => 'Hari Raya Nyepi',
                'deskripsi' => 'Libur Nasional Keagamaan Hindu'
            ]
        );

        HariLibur::updateOrCreate(
            ['tanggal' => Carbon::now()->addDays(14)->format('Y-m-d')],
            [
                'nama' => 'Wafat Isa Almasih',
                'deskripsi' => 'Libur Nasional Keagamaan Kristen'
            ]
        );

        // 8. IZIN GURU & SUBSTITUSI (SIMULASI)
        // Misalkan Ahmad mengajukan dinas luar untuk hari Senin depan
        $izinAhmad = IzinGuru::updateOrCreate(
            [
                'user_id' => $ahmad->id,
                'tanggal_mulai' => Carbon::now()->next('Monday')->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->next('Monday')->format('Y-m-d'),
            ],
            [
                'jenis_izin' => 'Dinas Luar',
                'alasan' => 'Pelatihan kurikulum merdeka tingkat provinsi.',
                'status' => 'Disetujui',
                'disetujui_oleh' => $admin->id,
                'catatan_admin' => 'Izin disetujui. Guru pengganti ditunjuk.'
            ]
        );

        // Tunjuk Budi sebagai guru pengganti untuk jadwal Ahmad tersebut
        SubstitusiJadwal::updateOrCreate(
            [
                'izin_guru_id' => $izinAhmad->id,
                'jadwal_id' => $jadwalAhmad->id,
                'tanggal' => Carbon::now()->next('Monday')->format('Y-m-d'),
            ],
            [
                'guru_pengganti_id' => $budi->id
            ]
        );
    }
}
