<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PREDIKSI (Langkah 1):
     * Jika tabel activities telah memiliki data dan category_id langsung dibuat wajib (NOT NULL),
     * maka migration akan GAGAL karena baris yang sudah ada tidak memiliki nilai category_id.
     *
     * STRATEGI MIGRASI DATA YANG AMAN:
     * 1. Tambahkan kolom category_id sebagai nullable terlebih dahulu.
     * 2. Buat kategori default atau mapping dari string 'category' lama ke ID kategori baru.
     * 3. Update semua baris yang ada agar memiliki category_id yang valid.
     * 4. Baru kemudian ubah kolom menjadi NOT NULL dan tambahkan foreign key constraint.
     *
     * Kolom dibuat nullable sementara agar data existing dapat dimigrasikan
     * sebelum constraint wajib dan foreign key diterapkan.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->after('id')
                ->nullable();
        });

        DB::table('activities')
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->each(function (string $categoryName): void {
                $slug = Str::slug($categoryName);
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $categoryName,
                    'slug' => $slug !== '' ? $slug : 'category-'.Str::random(8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('activities')
                    ->where('category', $categoryName)
                    ->update(['category_id' => $categoryId]);
            });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('category', 50)->after('id');
        });

        DB::table('activities')
            ->join('categories', 'categories.id', '=', 'activities.category_id')
            ->update(['activities.category' => DB::raw('categories.name')]);

        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
