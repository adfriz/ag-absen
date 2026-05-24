<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class TenStudentsSeeder extends Seeder
{
    public function run(): void
    {
        $class = Classroom::where('name', '10 IPA 1')->first();
        if (!$class) {
            $class = Classroom::create(['name' => '10 IPA 1', 'grade' => '10']);
        }

        $names = [
            'Ahmad Fauzi', 'Budi Santoso', 'Citra Lestari', 'Dewa Made', 'Eka Putri',
            'Fajar Ramadhan', 'Gita Permata', 'Hadi Wijaya', 'Indah Sari', 'Joko Susilo'
        ];

        foreach ($names as $index => $name) {
            Student::updateOrCreate(
                ['nisn' => '202400' . ($index + 1)],
                ['name' => $name, 'class_id' => $class->id]
            );
        }
    }
}
