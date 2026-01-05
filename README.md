# LPS VMI

Laporan Peraihan Siswa (Bimbel Gambar Villa Merah). Aplikasi dashboard dan API berbasis Laravel.

## Fitur Utama
- Dashboard ringkasan dan grafik (Chart.js)
- Data Siswa (tambah, edit, hapus)
- Data Leads + CRM Follow Up (tambah, edit, hapus)
- Target Market / Peraihan (tambah, edit, hapus)
- Manajemen User (khusus Super Admin)
- Log Activity (khusus Super Admin)
- Filter data berdasarkan wilayah, program, bulan, tahun, status

## Role dan Akses
| Role | Akses |
| --- | --- |
| super_admin | Full akses: CRUD semua data, Manajemen User, Log Activity |
| admin | CRUD data (Data Siswa, Data Leads, Target Market); tidak bisa Manajemen User dan Log Activity |
| user | Hanya lihat data; tidak bisa tambah/edit/hapus; tidak bisa Manajemen User dan Log Activity |

Catatan: Endpoint User hanya menerima role `admin` atau `user`. Untuk membuat `super_admin`, set kolom `users.role` langsung di database.

## Teknologi
- PHP 8.2+, Laravel 12, Sanctum
- Tailwind CSS (CDN) dan Chart.js

## Instalasi Lokal
Prasyarat: PHP 8.2+, Composer, Node.js, dan database (MySQL/MariaDB).

1. `composer install`
2. `copy .env.example .env` (Windows) atau `cp .env.example .env`
3. Atur koneksi database di `.env`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `npm install`
7. `npm run dev` (atau `npm run build`)
8. `php artisan serve`

Akses UI:
- `http://localhost:8000/`
- `http://localhost:8000/vmi`

## Akun Default (Seeder)
- username: `admin`
- password: `123`
- role: `admin`

## Autentikasi API
Semua endpoint di bawah `/api` (kecuali login) membutuhkan header:

```
Authorization: Bearer <token>
```

## Endpoint API

### Auth
- POST `/api/auth/login`
- POST `/api/auth/logout`
- GET `/api/me`

### Users (super_admin)
- GET `/api/users`
- POST `/api/users`
- GET `/api/users/{id}`
- PUT `/api/users/{id}`
- DELETE `/api/users/{id}`

### Data Siswa
- GET `/api/data-siswa`
- POST `/api/data-siswa` (admin dan super_admin)
- GET `/api/data-siswa/{id}`
- PUT `/api/data-siswa/{id}` (admin dan super_admin)
- DELETE `/api/data-siswa/{id}` (admin dan super_admin)

Filter query:
- `location`, `program`, `bulan`, `tahun`
- `date_field` (opsional: `tanggal_daftar` atau `created_at`)

### Data Leads
- GET `/api/data-leads`
- POST `/api/data-leads` (admin dan super_admin)
- GET `/api/data-leads/{id}`
- PUT `/api/data-leads/{id}` (admin dan super_admin)
- DELETE `/api/data-leads/{id}` (admin dan super_admin)

Filter query:
- `wilayah`, `program`, `status_crm`, `bulan`, `tahun`
- `date_field` (opsional: `tanggal_input` atau `created_at`)

### Target Market
- GET `/api/target-market`
- POST `/api/target-market` (admin dan super_admin)
- GET `/api/target-market/{id}`
- PUT `/api/target-market/{id}` (admin dan super_admin)
- DELETE `/api/target-market/{id}` (admin dan super_admin)

Filter query:
- `wilayah`, `program`, `bulan`, `tahun`

### Log Aktivitas
- GET `/api/log-aktivitas` (super_admin)
- POST `/api/log-aktivitas` (semua role terautentikasi)
- GET `/api/log-aktivitas/{id}` (super_admin)
- PUT `/api/log-aktivitas/{id}` (super_admin)
- DELETE `/api/log-aktivitas/{id}` (super_admin)

Filter query:
- `user_id`, `tanggal`, `bulan`, `tahun`

## Contoh Request (curl)
Ganti `BASE_URL` sesuai host Anda (contoh: `http://localhost:8000`).

### Login
```bash
curl -X POST "$BASE_URL/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"123"}'
```

### Get Profile (Me)
```bash
curl "$BASE_URL/api/me" \
  -H "Authorization: Bearer <token>"
```

### Tambah Data Siswa (admin/super_admin)
```bash
curl -X POST "$BASE_URL/api/data-siswa" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "location":"Bandung",
    "nama":"Nama Siswa",
    "no_hp_siswa":"08123456789",
    "asal_sekolah":"SMA 1",
    "kelas":"12 IPA 1",
    "tanggal_daftar":"2026-01-05",
    "biaya_pendidikan":20000000,
    "sisa_angsuran":5000000
  }'
```

### Tambah Data Leads (admin/super_admin)
```bash
curl -X POST "$BASE_URL/api/data-leads" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "wilayah":"Bandung",
    "nama_siswa":"Nama Lead",
    "asal_sekolah":"SMA 1",
    "no_hp":"08123456789",
    "program":"Program SR",
    "status_crm":"Prospek"
  }'
```

### Tambah Target Market (admin/super_admin)
```bash
curl -X POST "$BASE_URL/api/target-market" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "wilayah":"Bandung",
    "program":"SR Gold",
    "bulan":1,
    "tahun":2026,
    "target_siswa":30,
    "target_omset":120000000
  }'
```

### List Log Aktivitas (super_admin)
```bash
curl "$BASE_URL/api/log-aktivitas?bulan=1&tahun=2026" \
  -H "Authorization: Bearer <token>"
```

## Lisensi
Tentukan sesuai kebutuhan.
