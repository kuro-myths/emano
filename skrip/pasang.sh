#!/usr/bin/env bash
# Menyiapkan kerangka Laravel di repositori ini tanpa menimpa berkas buatan sendiri.
# Prasyarat: php (>= versi minimum Laravel), composer, rsync, node/npm.
set -euo pipefail

AKAR="$(cd "$(dirname "$0")/.." && pwd)"
SEMENTARA="$(mktemp -d)"
trap 'rm -rf "$SEMENTARA"' EXIT

echo "==> Membuat kerangka Laravel terbaru di direktori sementara"
composer create-project laravel/laravel "$SEMENTARA/dasar" --no-interaction

echo "==> Menyalin kerangka (berkas buatan sendiri tidak ditimpa)"
rsync -a --ignore-existing --exclude='.env' --exclude='.git/' --exclude='database/database.sqlite' "$SEMENTARA/dasar/" "$AKAR/"

cd "$AKAR"
echo "==> Menghapus berkas bawaan yang digantikan (tabel pengguna berbahasa Indonesia)"
rm -f app/Models/User.php \
      database/factories/UserFactory.php \
      database/migrations/0001_01_01_000000_create_users_table.php \
      tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php

composer require livewire/livewire --no-interaction

[ -f .env ] || cp .env.example .env
php artisan key:generate

echo
echo "Selesai. Langkah berikutnya:"
echo "  1. Sesuaikan DB_* di .env, pastikan PostgreSQL dan Redis berjalan"
echo "  2. php artisan migrate --seed"
echo "  3. npm install && npm run build"
echo "  4. php artisan test"
