# Deployment Checklist

1. Gunakan PHP 8.3+, MySQL 8+, Composer, Node.js, dan worker queue.
2. Salin `.env.example` ke `.env`, isi koneksi MySQL, mail, Midtrans, RajaOngkir, dan Turnstile.
3. Jalankan `composer install --no-dev --optimize-autoloader`, `npm ci`, dan `npm run build`.
4. Jalankan `php artisan migrate --force` dan, untuk instalasi baru, `php artisan db:seed --force` bila data demo diperlukan. Migration transisi akan memindahkan produk lama ke default variant secara otomatis; jangan menjalankan `migrate:fresh` pada database yang sudah berisi order.
5. Jalankan `php artisan storage:link`, `php artisan optimize`, dan worker `php artisan queue:work --tries=3` melalui Supervisor/systemd.
6. Atur webhook Midtrans ke `https://domain.toko/api/midtrans/webhook`. Jangan menggunakan callback browser sebagai sumber status pembayaran.
7. Pastikan `APP_URL`, `MAIL_FROM_*`, `RAJAONGKIR_ORIGIN_ID`, dan mode sandbox/production Midtrans sesuai environment.
8. Aktifkan Turnstile hanya setelah site key dan secret key untuk domain deployment sudah terpasang.
9. Gunakan HTTPS dan set `SESSION_SECURE_COOKIE=true` pada production.
10. Backup database dan storage sebelum deployment pembaruan.

Invoice disimpan sebagai HTML print-ready dan dilampirkan ke email. Pengguna dapat memakai menu **Cetak / Simpan PDF** dari browser. Jika dibutuhkan PDF biner di server, tambahkan library PDF Laravel yang kompatibel dengan Laravel 13 pada deployment berikutnya.
