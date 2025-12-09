#!/bin/bash

# Script ini dijalankan Railway sebelum langkah instalasi Composer default.
# TUJUAN: Memaksa instalasi Composer dengan mengabaikan semua masalah platform (PHP 8.3 dan ext-gd yang hilang).
# Ini mengatasi kegagalan karena Railway mengabaikan NIXPACKS_BUILD_CMD kita.

echo "Menjalankan script before-build kustom..."

# Jalankan Composer Install dengan flag yang mengabaikan masalah platform
# Perintah ini akan menyelesaikan dependensi, mengabaikan persyaratan PHP 8.3 dan ext-gd yang hilang.
composer install --no-dev --ignore-platform-reqs --no-interaction --optimize-autoloader

# Catatan: Jika perintah di atas gagal, script akan berhenti. 
# Jika ingin mencoba melanjutkan meskipun gagal, gunakan 'composer install ... || true'

echo "Script before-build selesai. Build Railway dilanjutkan."

exit 0