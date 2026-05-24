<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Kelas
        $class10 = \App\Models\Classroom::create(['name' => '10 IPA 1', 'grade' => '10']);
        $class11 = \App\Models\Classroom::create(['name' => '11 IPS 2', 'grade' => '11']);

        // 2. Buat Siswa untuk Kelas 10
        for ($i = 1; $i <= 15; $i++) {
            \App\Models\Student::create([
                'nisn' => '100' . $i,
                'name' => 'Siswa Kelas 10 - ' . $i,
                'class_id' => $class10->id,
            ]);
        }

        // 3. Buat Siswa untuk Kelas 11
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Student::create([
                'nisn' => '110' . $i,
                'name' => 'Siswa Kelas 11 - ' . $i,
                'class_id' => $class11->id,
            ]);
        }

        // 4. Ambil User Guru yang sudah kita buat tadi
        $guru = \App\Models\User::where('username', 'guru')->first();

        if ($guru) {
            // Ambil nama hari ini dalam Bahasa Inggris (sesuai format PHP 'l')
            $today = date('l');

            // 5. Buat Jadwal untuk Guru tersebut hari ini
            \App\Models\Schedule::create([
                'user_id' => $guru->id,
                'class_id' => $class10->id,
                'day' => $today,
                'start_time' => '07:30:00',
                'end_time' => '09:00:00',
            ]);

            \App\Models\Schedule::create([
                'user_id' => $guru->id,
                'class_id' => $class11->id,
                'day' => $today,
                'start_time' => '09:30:00',
                'end_time' => '11:00:00',
            ]);
        }
    }
}
