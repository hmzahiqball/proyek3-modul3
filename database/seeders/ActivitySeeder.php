<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::query()->insert([
            [
                'title' => 'Workshop Git Dasar',
                'description' => 'Latihan kolaborasi repository.',
                'activity_date' => '2026-10-05',
                'category' => 'Workshop',
                'status' => 'Planned',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Seminar Web Quality',
                'description' => 'Pengenalan maintainability dan testing.',
                'activity_date' => '2026-10-12',
                'category' => 'Seminar',
                'status' => 'Planned',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Belajar Laravel Basic',
                'description' => 'Membuat kerangka CRUD Laravel.',
                'activity_date' => '2026-10-15',
                'category' => 'Belajar',
                'status' => 'Ongoing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mengerjakan Proyek Modul 2',
                'description' => 'Membuat halaman interaktif dengan JS.',
                'activity_date' => '2026-10-01',
                'category' => 'Tugas',
                'status' => 'Done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Rapat Tim Mingguan',
                'description' => 'Koordinasi progres proyek mingguan.',
                'activity_date' => '2026-10-08',
                'category' => 'Meeting',
                'status' => 'Ongoing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
