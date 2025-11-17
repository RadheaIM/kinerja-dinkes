Panduan Instalasi (Deployment)

Aplikasi Laporan Kinerja Unit Puskesmas dan Labkesda

Dinas Kesehatan Kabupaten Garut

1. Pendahuluan

Dokumen ini menjelaskan langkah-langkah teknis untuk melakukan instalasi (deployment) aplikasi Laporan Kinerja Dinkes Garut di server produksi (hosting).

Aplikasi ini dibangun menggunakan framework Laravel 10 dengan PHP 8.0.13.

2. Persyaratan Server

Server produksi (Diskominfo) harus memenuhi persyaratan minimum berikut:

Web Server: Apache atau Nginx

PHP: Versi 8.0.13 atau lebih tinggi

Database: MySQL

Ekstensi PHP (Wajib):

pdo_mysql (untuk koneksi DB)

mbstring

xml

fileinfo (untuk upload file)

gd (untuk image processing, jika ada)

curl

openssl

3. Paket Serah Terima

Paket ini berisi 3 komponen utama yang diserahkan oleh tim pengembang :

kinerja-dinkes2.zip:

Berisi seluruh source code aplikasi Laravel.

TIDAK menyertakan folder /vendor.

TIDAK menyertakan file .env (demi keamanan).

MENYERTAKAN file .env.example sebagai panduan konfigurasi.

kinerja_dinkes.sql:

File ekspor database (.sql) dari lingkungan pengembangan.

Database ini sudah BERSIH dari data uji coba (laporan testing).

Database ini sudah TERISI data master (data penting) yang siap pakai, termasuk:

Tabel users (Berisi 1 Akun Admin Dinkes, 1 Akun Labkesda, dan 67 Akun Puskesmas).

Tabel sasaran_puskesmas (Berisi 67 nama Puskesmas untuk dropdown).

Tabel-tabel lain yang diperlukan (rhk_kapus, migrations, dll).

README.md:

File ini (panduan instalasi).

4. Langkah-Langkah Instalasi di Server Produksi

Berikut adalah urutan langkah yang harus dilakukan oleh Administrator Server (Diskominfo) untuk mendeploy aplikasi ini.

Langkah 1: Persiapan Database

Buat sebuah database MySQL baru di server (misal: db_kinerja_dinkes).

Buka database tersebut.

Impor (Import) file database_master.sql yang telah disediakan ke dalam database baru ini.

Langkah 2: Upload dan Konfigurasi Kode

Upload file kode_aplikasi.zip ke direktori web server (misal: /var/www/laporankinerja).

Ekstrak file .zip tersebut.

Salin file .env.example menjadi file .env baru.

cp .env.example .env


Buka file .env yang baru dibuat (nano .env) dan konfigurasi variabel berikut:

# --- Detail Aplikasi ---
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=[https://laporankinerja.dinkesgarut.go.id](https://laporankinerja.dinkesgarut.go.id) # (Ganti dengan URL/domain resmi)

# --- Detail Database (WAJIB) ---
# (Sesuaikan dengan nama DB dan user DB yang dibuat di Langkah 1)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kinerja_dinkes
DB_USERNAME=user_db_server
DB_PASSWORD=password_db_server


Langkah 3: Instalasi Dependensi (via Terminal)

Pindah ke direktori aplikasi (cd /var/www/laporankinerja) dan jalankan perintah-perintah berikut:

Instalasi Library (Vendor):

composer install --optimize-autoloader --no-dev


(Perintah ini akan membuat folder /vendor baru yang bersih di server).

Generate Kunci Aplikasi:
(Perintah ini akan mengisi APP_KEY yang kosong di file .env).

php artisan key:generate


Buat Storage Link (WAJIB):
(Perintah ini penting agar file Bukti Dukung yang di-upload bisa diakses).

php artisan storage:link


Langkah 4: Optimasi dan Cache (Mode Produksi)

Jalankan perintah ini untuk membuat aplikasi berjalan cepat dan membaca konfigurasi baru:

php artisan config:cache
php artisan route:cache
php artisan view:cache


Langkah 5: Atur Izin (Permissions) Folder

Terakhir, pastikan server (misal: www-data) memiliki izin untuk menulis ke folder storage dan bootstrap/cache.

sudo chown -R www-data:www-data /var/www/laporankinerja
sudo chmod -R 775 /var/www/laporankinerja/storage
sudo chmod -R 775 /var/www/laporankinerja/bootstrap/cache


5. Akun Administrator Default

Aplikasi ini sudah siap diakses. Akun Administrator utama yang terdapat di database_master.sql adalah:

Email: admin@gmail.com

Password: admin12345 (Mohon ganti password ini segera setelah login pertama kali melalui menu "Profil Pengguna")

Hak Cipta © {{ date('Y') }} Dinas Kesehatan Kabupaten Garut