# Activity Manager

Aplikasi Manajemen Kegiatan (Modul 3 Framework in Programming). 

## Persiapan & Menjalankan Proyek

1. **Install dependensi (jika baru di-*clone*)**
   ```bash
   composer install
   ```

2. **Setup file konfigurasi**
   Copy file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   *Atau jika menggunakan Windows Command Prompt:*
   ```cmd
   copy .env.example .env
   ```

3. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

4. **Setup Database & Seeder**
   Aplikasi ini menggunakan SQLite secara default. Jalankan migrasi dan seeding data:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## URL Route Utama
Setelah server berjalan, Anda dapat mengakses aplikasi di browser melalui URL utama berikut:
- **Daftar Kegiatan:** `http://localhost:8000/activities`
- **Daftar Kegiatan dengan Filter:** `http://localhost:8000/activities?status=Planned`
- **Tambah Kegiatan:** `http://localhost:8000/activities/create`

## Task 3 - Quality Review

- Soft delete menggunakan `Activity::withTrashed()` dan `Activity::onlyTrashed()`; halaman index memakai query normal agar data terhapus tidak tampil.
- Query index menggunakan `with('category')` karena view menampilkan nama kategori. Test query membuktikan lazy loading menghasilkan 11 query, sedangkan eager loading menghasilkan 2 query untuk 10 activity.
- SonarQube belum dapat dijalankan pada environment ini karena `sonar-scanner` tidak tersedia dan repository tidak memiliki konfigurasi SonarQube. Pemeriksaan otomatis yang tersedia tetap dijalankan melalui PHPUnit: seluruh test lulus.
