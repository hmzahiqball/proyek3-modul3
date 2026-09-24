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
