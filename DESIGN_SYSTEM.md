# Design System

Seluruh token tema berada di `resources/css/app.css` pada `:root`. Ubah token tersebut untuk mengganti warna, radius, spacing, shadow, dan focus state secara global.

## Komponen utama

- `<x-ui.button>`: gunakan variant `primary`, `secondary`, `success`, `warning`, `danger`, `outline`, atau `ghost`.
- `<x-ui.card>`: wrapper konten, form, ringkasan, dan informasi.
- `<x-ui.badge>`: status ringkas.
- `<x-ui.alert>`: pesan tetap di dalam halaman.
- `<x-ui.toast>`: feedback sesudah action.
- `<x-ui.confirm-action>`: wajib untuk action destruktif.
- `<x-ui.searchable-select>`: select dengan pencarian, clear, loading, empty, disabled, dan multiple state.

## Class reusable

- `.ui-field` dan `.ui-field-label` untuk input form.
- `.ui-form-actions` untuk posisi tombol form.
- `.ui-table-wrap` dan `.ui-table` untuk seluruh tabel.
- `.ui-table-actions` untuk tombol action pada tabel.
- `.ui-card-header`, `.ui-card-body`, dan `.ui-card-footer` untuk struktur card.

Tabel dengan banyak kolom menggunakan `min-width` yang sesuai di dalam `.ui-table-wrap`, sehingga tetap dapat digunakan melalui horizontal scrolling pada tablet dan mobile.

## Bahasa UI

Nama class, enum, model, dan status internal menggunakan Bahasa Inggris. Label yang tampil kepada pengguna berasal dari method `label()` pada enum di `app/Enums` atau ditulis dalam Bahasa Indonesia yang natural.
