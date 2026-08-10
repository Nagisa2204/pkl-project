# Deployment

## Prasyarat

- PHP 8.3 atau lebih baru.
- MySQL sebagai database utama.
- Composer dan Node.js/npm.
- Queue worker aktif untuk email dan invoice.
- Credential Midtrans, RajaOngkir, Turnstile, dan SMTP pada `.env`.

## Langkah deployment

1. Backup database dan direktori `storage/app/public`.
2. Pastikan source deployment tidak menyertakan `.env` dari komputer lain.
3. Jalankan:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize:clear
   php artisan optimize
   ```

4. Aktifkan atau restart queue worker.
5. Login sebagai admin, buka **Pengaturan Toko**, kemudian lengkapi **Alamat Pickup Toko** sampai kelurahan/desa.
6. Uji perhitungan ongkir dari alamat pickup toko ke alamat pelanggan.
7. Uji pembayaran Midtrans sandbox dan pastikan webhook menuju `/api/midtrans/webhook`.

## Verifikasi

Jalankan sebelum production:

```bash
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

Jangan menjalankan `migrate:fresh` pada database yang sudah berisi transaksi.
