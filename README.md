# BuildSync ERP
Sistem ERP berbasis Laravel untuk manajemen proyek konstruksi.

## Modul
- Proyek, jadwal, progres, dan laporan.
- Tenaga kerja, penugasan per proyek/tahapan, kehadiran, dan biaya upah.
- Inventaris material dan histori mutasi stok.
- Supplier, purchase order, penerimaan barang, dan riwayat pembelian.
- Anggaran biaya dan arus kas, termasuk pembayaran supplier dan upah.
- Hak akses berbasis peran.

## Role
| Role | Akses |
|---|---|
| Superadmin | Semua modul dan manajemen role |
| Project Manager | Proyek, jadwal, progres, SDM, laporan |
| Logistics | Material, mutasi, supplier, pengadaan, penerimaan |
| Finance | Anggaran, arus kas, pembayaran PO, laporan |
| Field Staff | Pencatatan kehadiran |

## Setup
1. Salin .env.example menjadi .env dan atur database.
2. Jalankan composer install dan npm install.
3. Jalankan php artisan key:generate.
4. Jalankan php artisan migrate.
5. Jalankan php artisan db:seed.
6. Jalankan npm run build.

Untuk database yang sudah berisi data, cukup jalankan migrasi. Jangan menjalankan db:seed jika tidak ingin memperbarui ulang password admin menjadi password default.

Seeder awal: admin@buildsync.com / password. Ganti password sebelum deployment.
