<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::query()->insert([
            [
                'name' => 'Workshop',
                'slug' => 'workshop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Seminar',
                'slug' => 'seminar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pelatihan',
                'slug' => 'pelatihan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
