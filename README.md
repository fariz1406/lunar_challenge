## Requirement server
* Laravel yang saya gunakan adalah laravel 11
* PHP Versi 8.2 atau lebih baru.
* Composer Terinstal.
* Database MySQL.

## cara setup project

1.  Clone Repository

2.  Install Dependencies

3.  Konfigurasi Environment (.env)
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=lunar_challenge
    DB_USERNAME=root
    DB_PASSWORD=

    QUEUE_CONNECTION=database

5.  Migrate Database

### Cara Menjalankan Aplikasi

Aplikasi ini membutuhkan dua terminal yang berjalan bersamaan agar proses import di latar belakang bisa bekerja.

## Terminal 1: Menjalankan Server API
php artisan serve

## Terminal 2: Menjalankan Background Worker
php artisan queue:work

Tools Testing (Data Dummy)
Project ini ada command khusus untuk membuat file CSV dummy untuk keperluan testing upload.

Generate jumlah custom (contoh 10.000 data)
php artisan make:dummy-csv 10000

File akan tersimpan di: storage/app/dummy_users.csv

## Dokumentasi API

1. Upload CSV
Endpoint untuk mengunggah file CSV dan memasukkannya ke antrian.
- URL: POST /api/upload-csv
- Headers: Accept: application/json
- Body (form-data):

Response Sukses (202 Accepted):

{
    "message": "Processing",
    "import_id": "uuid-string...",
    "endpoint_check": "http://localhost:8000/api/import-status/uuid-string..."
}

2. Cek Status Import
Endpoint untuk memantau progress import secara real-time.
URL: GET /api/import-status/{import_id}

Response Sukses (200 OK):
{
    "status": "processing",
    "processed": 5000,
    "total": 100000,
    "message": "Processed 5000 of 100000 rows"
}

Status akan berubah menjadi completed saat selesai.