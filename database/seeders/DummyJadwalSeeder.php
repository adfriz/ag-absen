<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Jadwal;
use Illuminate\Database\Seeder;

class DummyJadwalSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAktif = TahunAjaran::query()->where('apakah_aktif', '=', true)->first();
        if (!$tahunAktif) {
            $tahunAktif = TahunAjaran::query()->create([
                'tahun' => '2025/2026',
                'semester' => 'Ganjil',
                'apakah_aktif' => true,
            ]);
        }

        $gurus = User::query()->where('role', '=', 'guru')->get();
        if ($gurus->isEmpty()) {
            // Create some default gurus if none exist
            $names = ['Ahmad S.Pd.', 'Budi S.Pd.', 'Citra S.Pd.', 'Dewi S.Pd.', 'Eko S.Pd.', 'Fitri S.Pd.'];
            foreach ($names as $idx => $name) {
                $username = 'guru' . ($idx + 1);
                $gurus->push(User::create([
                    'name' => $name,
                    'email' => $username . '@alghazaly.com',
                    'username' => $username,
                    'password' => bcrypt('password'),
                    'role' => 'guru',
                ]));
            }
        }

        $classes = Kelas::all();
        if ($classes->isEmpty()) {
            $classNames = ['X IPA 1', 'X IPA 2', 'XI IPA 1', 'XI IPA 2', 'XII IPA 1', 'XII IPA 2'];
            foreach ($classNames as $name) {
                $classes->push(Kelas::create([
                    'nama' => $name,
                    'tingkat' => preg_match('/X+/i', $name, $matches) ? (strlen($matches[0]) == 3 ? '12' : (strlen($matches[0]) == 2 ? '11' : '10')) : '10',
                ]));
            }
        }

        $subjects = MataPelajaran::all();
        if ($subjects->isEmpty()) {
            $subjData = [
                ['kode' => 'MTK', 'nama' => 'Matematika'],
                ['kode' => 'FIS', 'nama' => 'Fisika'],
                ['kode' => 'KIM', 'nama' => 'Kimia'],
                ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia'],
                ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
                ['kode' => 'BIO', 'nama' => 'Biologi'],
                ['kode' => 'SEJ', 'nama' => 'Sejarah'],
            ];
            foreach ($subjData as $s) {
                $subjects->push(MataPelajaran::create($s));
            }
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $slots = [
            ['mulai' => '07:30:00', 'selesai' => '09:00:00'],
            ['mulai' => '09:15:00', 'selesai' => '10:45:00'],
            ['mulai' => '11:00:00', 'selesai' => '12:30:00'],
            ['mulai' => '13:00:00', 'selesai' => '14:30:00'],
            ['mulai' => '15:00:00', 'selesai' => '16:30:00'],
            ['mulai' => '16:45:00', 'selesai' => '18:15:00'],
            ['mulai' => '18:30:00', 'selesai' => '20:00:00'],
            ['mulai' => '20:15:00', 'selesai' => '21:45:00'],
            ['mulai' => '22:00:00', 'selesai' => '23:30:00'],
            ['mulai' => '23:45:00', 'selesai' => '01:15:00'],
            ['mulai' => '01:30:00', 'selesai' => '03:00:00'],
            ['mulai' => '03:15:00', 'selesai' => '04:45:00'],
        ];

        // Seed many schedules without overlapping teacher, or classroom in same slot + day
        $count = 0;
        foreach ($days as $day) {
            foreach ($slots as $slotIdx => $slot) {
                // Keep track of which teachers and classes are busy in this day/slot
                $busyTeachers = [];
                $busyClasses = [];

                foreach ($gurus as $guru) {
                    if (in_array($guru->id, $busyTeachers)) continue;

                    // Find a class that is not busy
                    $availableClass = null;
                    foreach ($classes as $class) {
                        if (!in_array($class->id, $busyClasses)) {
                            $availableClass = $class;
                            break;
                        }
                    }

                    if (!$availableClass) break; // No more classes available in this slot

                    // Pick a random subject
                    $subject = $subjects->random();

                    // Create or update schedule
                    Jadwal::updateOrCreate(
                        [
                            'user_id' => $guru->id,
                            'kelas_id' => $availableClass->id,
                            'tahun_ajaran_id' => $tahunAktif->id,
                            'hari' => $day,
                            'jam_mulai' => $slot['mulai'],
                            'jam_selesai' => $slot['selesai'],
                        ],
                        [
                            'mata_pelajaran_id' => $subject->id,
                        ]
                    );

                    $busyTeachers[] = $guru->id;
                    $busyClasses[] = $availableClass->id;
                    $count++;
                }
            }
        }

        echo "Successfully seeded {$count} dummy schedules across {$days[0]}-{$days[count($days) - 1]}.\n";
    }
}
