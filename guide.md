# Panduan Lengkap Proyek Sistem Absensi Al-Ghazaly

Sistem Absensi Al-Ghazaly adalah aplikasi berbasis web yang dibangun dengan Laravel 10 dan Filament PHP v3 untuk mengelola absensi siswa, penjadwalan guru, penggantian guru piket/substitusi, serta tracking rekap absensi secara real-time.

---

## 1. Peran & Akses Pengguna (Roles)

Sistem memiliki dua peran utama dengan akses yang terpisah:

### A. Administrator (Admin)
Mengelola seluruh data master sekolah di panel admin:
* **User**: Mengelola akun guru dan staf.
* **Siswa**: Mengelola data siswa, pembagian kelas per tahun ajaran aktif, dan nomor absen.
* **Kelas & Mata Pelajaran**: Mengatur kelas yang aktif dan mata pelajaran.
* **Tahun Ajaran**: Mengontrol tahun ajaran yang sedang aktif dan semester.
* **Hari Libur**: Mengatur hari libur nasional/sekolah untuk menonaktifkan pengisian absen secara otomatis.
* **Jadwal**: Menyusun jadwal pelajaran reguler.
* **Persetujuan Izin Guru (IzinGuru)**: Menyetujui atau menolak izin mengajar guru.
* **Dashboard Admin (StatsOverview)**: Melihat jumlah total siswa, kelas aktif, dan statistik kehadiran hari ini secara langsung.

### B. Guru
Mengelola aktivitas pembelajaran harian:
* **Dashboard Guru (TodaySchedule)**: Melihat daftar jam mengajar hari ini, status pengisian presensi kelas, dan tombol cepat "Masuk Kelas".
* **Isi Presensi**: Mengisi data kehadiran siswa pada jam pelajaran tertentu.
* **Pengajuan Izin**: Mengajukan izin jika berhalangan mengajar (sakit, cuti, dll.) yang otomatis memicu alur guru pengganti jika disetujui.
* **Jadwal Saya**: Melihat rangkuman jadwal mengajar reguler milik guru tersebut.

---

## 2. Struktur Database & Model Data

Aplikasi ini menggunakan 10 model utama dengan relasi sebagai berikut:

### 1. `User` (Guru & Staf)
* Menyimpan kredensial login dan `role` (`admin` / `guru`).

### 2. `Siswa`
* Menyimpan NISN, nama lengkap, dan jenis kelamin.
* Berelasi **Many-to-Many** dengan `Kelas` melalui tabel pivot `kelas_siswa` yang menampung `tahun_ajaran_id` dan `nomor_absen` untuk menjaga historis data setiap tahun ajaran.

### 3. `Kelas`
* Menyimpan nama kelas (contoh: X-A, XI-B).
* Berelasi dengan `Siswa` per tahun ajaran.

### 4. `Mata Pelajaran`
* Menyimpan nama mata pelajaran.

### 5. `TahunAjaran`
* Menyimpan tahun (contoh: "2025/2026") dan semester ("Ganjil"/"Genap").
* Kolom `apakah_aktif` (boolean) menandai tahun ajaran yang sedang berjalan untuk memfilter data kelas dan presensi secara dinamis.

### 6. `HariLibur`
* Menyimpan daftar tanggal libur nasional/sekolah. Jika hari ini terdaftar sebagai hari libur, pengisian absen di hari tersebut otomatis diblokir.

### 7. `Jadwal`
* Menghubungkan `Kelas`, `MataPelajaran`, `User` (Guru), `TahunAjaran`, `hari`, serta `jam_mulai` dan `jam_selesai`.

### 8. `IzinGuru`
* Digunakan oleh guru untuk mengajukan izin berhalangan mengajar pada rentang tanggal tertentu.
* Memiliki `status` (`Pending`, `Disetujui`, `Ditolak`).

### 9. `SubstitusiJadwal`
* Jika pengajuan izin guru disetujui admin, admin akan menentukan guru pengganti (`guru_pengganti_id`) untuk jadwal-jadwal tertentu pada tanggal izin tersebut.
* Data ini dicatat di tabel `substitusi_jadwal`.

### 10. `Presensi`
* Menyimpan data absensi siswa per sesi jadwal pelajaran pada tanggal tertentu.
* Kolom `status` bernilai:
  * `H` (Hadir)
  * `S` (Sakit) — *Membutuhkan unggah bukti surat*
  * `I` (Izin) — *Membutuhkan unggah bukti surat*
  * `A` (Alpa)
  * `D` (Dispensasi) — *Membutuhkan catatan tugas dispensasi*
  * `T` (Terlambat) — *Membutuhkan input durasi keterlambatan dalam menit*

---

## 3. Fitur Utama & Logika Bisnis

### A. Alur Guru Pengganti (Substitusi)
1. **Pengajuan**: Guru membuat pengajuan izin berhalangan mengajar di menu **Pengajuan Izin**.
2. **Approval**: Admin meninjau pengajuan di menu **Izin Guru**. Saat menyetujui, Admin mengisi form detail untuk menetapkan guru pengganti pada jadwal mengajar guru yang izin.
3. **Perubahan Dashboard**: Pada hari izin tersebut, jadwal mengajar di dashboard guru yang izin akan hilang, dan otomatis muncul di dashboard **guru pengganti** dengan label "(Guru Pengganti)" sehingga guru pengganti dapat melakukan presensi kelas tersebut.

### B. Validasi Waktu Pengisian Absen
1. **Hari Libur**: Jika tanggal yang dipilih adalah hari libur (terdaftar di model `HariLibur`), tombol pengisian absen dikunci dan muncul banner pemberitahuan.
2. **Belum Waktunya**: Guru tidak bisa menekan tombol "Masuk Kelas" atau mengisi absensi sebelum jam pelajaran (`jam_mulai`) dimulai pada hari H.
3. **Batas Toleransi Koreksi (H+3)**: Guru diberikan waktu maksimal 3 hari (H+3) sejak tanggal pelajaran untuk mengoreksi absensi atau mengisi absensi susulan. Setelah melewati batas H+3:
   * Status presensi terkunci otomatis.
   * Hanya status `A` (Alpa), `S` (Sakit), atau `I` (Izin) yang boleh diubah untuk melengkapi bukti surat siswa yang menyusul.

### C. Responsivitas Halaman Presensi
Halaman pengisian presensi (`IsiPresensi.php`) memiliki dua tipe tampilan visual:
* **Desktop**: Menggunakan tabel lebar yang rapi.
* **Mobile**: Menggunakan layout berbasis kartu (card layout) yang ringkas dan ramah sentuhan (touch-friendly).
* Tombol status kehadiran diberi warna khusus (Success untuk Hadir, Warning untuk Sakit/Terlambat, Info untuk Izin/Dispensasi, Danger untuk Alpa) agar memudahkan visualisasi saat di-tap.

---

## 4. Setup Awal & Cara Menjalankan

Langkah-langkah untuk menjalankan proyek ini secara lokal:

1. **Instal Dependensi**:
   ```bash
   composer install
   npm install && npm run dev
   ```

2. **Konfigurasi Environment**:
   Salin `.env.example` menjadi `.env` lalu sesuaikan kredensial database Anda.

3. **Migrasi Database & Seeder**:
   Gunakan seeder bawaan untuk mengisi data dummy admin, guru, kelas, siswa, mata pelajaran, dan jadwal awal:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Kredensial Default Seeder**:
   * **Admin**: `admin@alghazaly.com` (password: `password`)
   * **Guru 1 (Ahmad)**: `ahmad@alghazaly.com` (password: `password`)
   * **Guru 2 (Budi)**: `budi@alghazaly.com` (password: `password`)
   * **Guru 3 (Citra)**: `citra@alghazaly.com` (password: `password`)

---

## 5. Panduan Deploy & Build (Produksi)

Berikut adalah langkah-langkah untuk mendeploy aplikasi ini ke server produksi (VPS, Shared Hosting, atau cPanel):

### A. Build Aset Frontend (Vite)
Aset CSS/JS harus di-compile untuk produksi agar load web cepat:
```bash
npm run build
```
*Hasil build akan tersimpan di folder `public/build`.*

### B. Setup Server & Web Server
* **PHP Version**: Minimal PHP 8.1.
* **Document Root**: Arahkan domain/subdomain ke folder `/public` (bukan root proyek).
* **Ekstensi PHP Wajib**: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`.

### C. Langkah Deploy di Server (VPS/SSH)
1. **Clone Proyek** ke server.
2. **Install Composer** (lewati paket development):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. **Setup `.env` Produksi**:
   * Ubah `APP_ENV=production`
   * Ubah `APP_DEBUG=false`
   * Generate key jika belum ada: `php artisan key:generate`
   * Masukkan detail DB produksi.
4. **Migrasi Database**:
   ```bash
   php artisan migrate --force
   ```
5. **Hubungkan Folder Storage** (Penting untuk bukti surat):
   ```bash
   php artisan storage:link
   ```
6. **Optimasi Cache Laravel**:
   ```bash
   php artisan config:cache
   ```

### D. Khusus Shared Hosting (InfinityFree / Hostinger tanpa SSH)
Karena InfinityFree **tidak memiliki akses SSH/Terminal**, proses build dan composer harus disiapkan dari komputer lokal sebelum diunggah:

1. **Siapkan File di Lokal (PC Anda)**:
   * Jalankan build frontend: `npm run build`
   * Hapus folder `vendor` lalu install dependencies produksi saja:
     ```bash
     composer install --no-dev --optimize-autoloader
     ```
2. **Folder yang Wajib Diunggah ke File Manager**:
   * `app/`
   * `bootstrap/`
   * `config/`
   * `database/`
   * `public/` (pastikan folder `public/build` hasil build ada)
   * `resources/`
   * `routes/`
   * `vendor/` (wajib karena di server gratisan tidak bisa install composer)
   * `.env` (isi disesuaikan DB server)
   * `artisan`
   * `composer.json`

3. **Folder yang TIDAK BOLEH Diunggah (Hapus/Skip)**:
   * `node_modules/` (sangat besar dan tidak digunakan di server)
   * `.git/` (riwayat git)

4. **Penyesuaian Struktur Folder InfinityFree (htdocs)**:
   InfinityFree mengarahkan domain utama ke folder `/htdocs`. Agar Laravel berjalan aman tanpa mengekspos file sistem:
   * **Cara Paling Mudah (.htaccess)**: Upload seluruh folder & file di atas ke dalam folder `/htdocs`. Lalu buat file `.htaccess` di dalam root `/htdocs` dengan isi:
     ```apache
     <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteRule ^(.*)$ public/$1 [L]
     </IfModule>
     ```
    * **Alternatif (Pecah Folder)** (Sangat Direkomendasikan untuk Keamanan):
      1. Bikin folder baru sejajar dengan `htdocs` di File Manager (misal dinamakan `laravel-core`).
      2. Upload seluruh file & folder Laravel (kecuali folder `public`) ke dalam folder `laravel-core` tersebut.
      3. Upload isi dari folder `public` lokal (seperti `index.php`, `.htaccess`, `favicon.ico`, folder `build`, dll) langsung ke dalam folder `htdocs`.
      4. Buka file `/htdocs/index.php` di File Manager hosting, ubah baris berikut agar mengarah ke folder `laravel-core`:
         ```php
         // Baris 24 (atau di sekitar autoload):
         // Cari:
         require __DIR__.'/../vendor/autoload.php';
         // Ubah jadi:
         require __DIR__.'/../laravel-core/vendor/autoload.php';

         // Baris 38 (atau di sekitar bootstrap):
         // Cari:
         $app = require_once __DIR__.'/../bootstrap/app.php';
         // Ubah jadi:
         $app = require_once __DIR__.'/../laravel-core/bootstrap/app.php';
         ```
      5. Buka `/laravel-core/app/Providers/AppServiceProvider.php`, tambahkan kode ini di dalam method `register` agar path asset (Vite/Mix) tetap mengarah ke `/htdocs` (sebagai public path baru):
         ```php
         $this->app->bind('path.public', function() {
             return base_path() . '/../htdocs';
         });
         ```

### E. Setup Database di InfinityFree (Tanpa Terminal)
Karena di InfinityFree **tidak bisa menjalankan `php artisan migrate`**, ikuti langkah ini:

1. **Eksport DB Lokal**:
   * Jalankan seeder di lokal PC dulu agar data terisi: `php artisan migrate:fresh --seed`
   * Buka **phpMyAdmin** lokal (`localhost/phpmyadmin`).
   * Pilih database proyek absensi ini, klik menu **Export**, lalu unduh file `.sql` nya.
2. **Buat DB Baru di Hosting**:
   * Masuk ke Control Panel InfinityFree.
   * Pilih menu **MySQL Databases**, buat database baru.
   * Catat: **DB_HOST**, **DB_NAME**, **DB_USER**, dan **DB_PASSWORD** dari panel.
3. **Import SQL**:
   * Buka **phpMyAdmin** di panel InfinityFree.
   * Pilih database baru Anda, masuk menu **Import**, lalu upload file `.sql` dari PC tadi.
4. **Hubungkan `.env`**:
   * Buka file `.env` di hosting, ganti konfigurasi database sesuai detail dari InfinityFree:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=sqlxxx.infinityfree.com  <-- Lihat di panel
     DB_PORT=3306
     DB_DATABASE=if0_xxxxx_xxx        <-- Lihat di panel
     DB_USERNAME=if0_xxxxx            <-- Lihat di panel
     DB_PASSWORD=xxxxxxxxxx
     ```
