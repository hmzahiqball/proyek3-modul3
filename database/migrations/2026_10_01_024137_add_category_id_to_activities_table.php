<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
     * Karena ini database latihan yang bisa di-reset, kita gunakan migrate:fresh
     * sehingga aman langsung membuat kolom wajib.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->after('id')
                ->constrained()
                ->restrictOnDelete();

            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->string('category', 50)->after('id');
        });
    }
};
