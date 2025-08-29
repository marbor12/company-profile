# Event Gallery Database Integration

## Overview
Event gallery section pada halaman utama (`index.php`) telah diintegrasikan dengan database untuk menampilkan data event yang sebenarnya dari tabel `event` dan `documentation`.

## Perubahan yang Dilakukan

### 1. Modifikasi `index.php`
- Menambahkan koneksi database di bagian atas file
- Mengganti hardcoded event gallery dengan data dinamis dari database
- Menambahkan logika untuk menampilkan gambar dokumentasi event

### 2. Struktur Data yang Digunakan

#### Tabel `event`
- `id` - ID unik event
- `title` - Judul event
- `date` - Tanggal pelaksanaan event
- `audience` - Jumlah peserta yang diharapkan
- `venue` - Lokasi event
- `category` - Kategori event
- `type` - Tipe event

#### Tabel `documentation`
- `id_dokumentasi` - ID unik dokumentasi
- `id_event` - Foreign key ke tabel event
- `picture` - Nama file gambar dokumentasi

### 3. Fitur yang Diimplementasikan

#### a. Pengambilan Data Event
```php
$stmt = $pdo->query("
    SELECT 
        e.id,
        e.title,
        e.date,
        e.audience,
        e.venue,
        e.category,
        e.type,
        d.picture,
        d.id_dokumentasi
    FROM event e
    LEFT JOIN documentation d ON e.id = d.id_event
    WHERE e.date < CURDATE()  -- Only past events
    ORDER BY e.date DESC
    LIMIT 6
");
```

#### b. Penanganan Gambar
- Menggunakan gambar dokumentasi jika tersedia
- Fallback ke gambar default (`property/training.jpg`) jika tidak ada dokumentasi
- Error handling untuk gambar yang tidak dapat dimuat

#### c. Format Tanggal
- Menggunakan format Indonesia (contoh: "15 Januari 2025")
- Menggunakan class `DateTime` untuk formatting yang konsisten

#### d. Badge Warna Dinamis
Berdasarkan jumlah peserta:
- < 20 peserta: `bg-success` (hijau)
- 20-49 peserta: `bg-warning` (kuning)
- 50-99 peserta: `bg-info` (biru)
- 100-499 peserta: `bg-danger` (merah)
- ≥ 500 peserta: `bg-secondary` (abu-abu)

### 4. Tampilan yang Dihasilkan

Setiap card event gallery menampilkan:
- **Gambar**: Dokumentasi event atau gambar default
- **Judul Event**: Dari field `title` di database
- **Tanggal**: Format Indonesia dari field `date`
- **Jumlah Peserta**: Dari field `audience` dengan badge berwarna

### 5. Fallback dan Error Handling

#### Jika Tidak Ada Data Event
```php
<?php if (empty($event_gallery)): ?>
  <div class="col-12 text-center">
    <i class="fas fa-images" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
    <p class="text-muted">Belum ada dokumentasi event yang tersedia.</p>
  </div>
<?php endif; ?>
```

#### Jika Gambar Tidak Dapat Dimuat
```html
<img src="<?php echo htmlspecialchars($imageSrc); ?>" 
     alt="<?php echo htmlspecialchars($event['title']); ?>" 
     class="img-fluid" 
     onerror="this.src='property/training.jpg'" />
```

## Cara Menambahkan Event Gallery

### 1. Tambah Event Baru
1. Login ke admin panel
2. Buka menu "Events"
3. Klik "Add New Event"
4. Isi form dengan data event
5. Simpan event

### 2. Tambah Dokumentasi Event
1. Setelah event selesai, buka menu "Documentation"
2. Klik "Add New Documentation"
3. Pilih event yang sudah lewat
4. Upload gambar dokumentasi
5. Simpan dokumentasi

### 3. Event Akan Muncul Otomatis
- Event yang sudah lewat akan otomatis muncul di gallery
- Maksimal 6 event terbaru yang ditampilkan
- Urutan berdasarkan tanggal terbaru

## Keuntungan Integrasi Database

1. **Data Real-time**: Event gallery selalu menampilkan data terbaru
2. **Manajemen Mudah**: Admin dapat mengelola event dan dokumentasi melalui panel admin
3. **Konsistensi Data**: Data event gallery sama dengan data di admin panel
4. **Skalabilitas**: Mudah menambah event baru tanpa mengubah kode
5. **Maintenance**: Tidak perlu mengubah kode HTML untuk menambah event baru

## File yang Dimodifikasi

- `index.php` - Menambahkan koneksi database dan logika event gallery
- `Admin/config/database.php` - Koneksi database (sudah ada)
- `Admin/events.php` - Manajemen event (sudah ada)
- `Admin/documentation.php` - Manajemen dokumentasi (sudah ada)

## Catatan Penting

1. **Pastikan Database Terhubung**: File `Admin/config/database.php` harus dikonfigurasi dengan benar
2. **Folder Uploads**: Pastikan folder `uploads/documentation/` memiliki permission yang tepat
3. **Gambar Default**: File `property/training.jpg` harus tersedia sebagai fallback
4. **Event Past**: Hanya event yang sudah lewat yang ditampilkan di gallery
5. **Limit Data**: Maksimal 6 event yang ditampilkan untuk performa optimal
