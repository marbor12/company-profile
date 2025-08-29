# Stats Section Database Integration

## Overview
Bagian stats section pada halaman utama (`index.php`) telah diintegrasikan dengan database untuk menampilkan data real-time dari tabel `event`. Sekarang stats section menampilkan data yang sebenarnya dari database, bukan lagi hardcoded values.

## Perubahan yang Dilakukan

### 1. **Modifikasi Stats Section di `index.php`**
- Mengganti hardcoded values dengan variabel PHP yang mengambil data dari database
- Menambahkan query database untuk menghitung statistik yang diperlukan
- Menambahkan error handling untuk fallback values

### 2. **Data yang Diambil dari Database**

#### a. **Peserta Pelatihan**
- **Query**: `SELECT SUM(audience) as total FROM event WHERE audience IS NOT NULL`
- **Penjelasan**: Menjumlahkan semua field `audience` dari tabel event
- **Filter**: Hanya event yang memiliki nilai audience (tidak NULL)
- **Output**: Total jumlah peserta dari semua event yang telah dilaksanakan

#### b. **Program Pelatihan**
- **Query**: `SELECT COUNT(*) as total FROM event`
- **Penjelasan**: Menghitung total jumlah event yang ada di database
- **Output**: Total program pelatihan/event yang telah dibuat

#### c. **Mitra Strategis**
- **Query**: `SELECT COUNT(DISTINCT mitra) as total FROM event WHERE mitra IS NOT NULL AND mitra != ''`
- **Penjelasan**: Menghitung jumlah mitra unik dari field `mitra`
- **Filter**: Hanya mitra yang tidak NULL dan tidak kosong
- **Output**: Total mitra strategis yang berbeda

### 3. **Implementasi Kode**

#### a. **Query Database**
```php
// Get statistics for stats section
try {
    // Get total events (program pelatihan)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM event");
    $total_events = $stmt->fetch()['total'];
    
    // Get total audience (peserta pelatihan) - sum of all audience from events
    $stmt = $pdo->query("SELECT SUM(audience) as total FROM event WHERE audience IS NOT NULL");
    $total_audience = $stmt->fetch()['total'] ?? 0;
    
    // Get total unique partners (mitra strategis)
    $stmt = $pdo->query("SELECT COUNT(DISTINCT mitra) as total FROM event WHERE mitra IS NOT NULL AND mitra != ''");
    $total_partners = $stmt->fetch()['total'];
    
} catch(PDOException $e) {
    // Fallback values if database query fails
    $total_events = 0;
    $total_audience = 0;
    $total_partners = 0;
    error_log("Error getting stats: " . $e->getMessage());
}
```

#### b. **HTML Output**
```php
<div class="stat-number"><?php echo number_format($total_audience); ?>+</div>
<div class="stat-number"><?php echo $total_events; ?>+</div>
<div class="stat-number"><?php echo $total_partners; ?>+</div>
```

### 4. **Fitur yang Diimplementasikan**

#### a. **Data Real-time**
- Stats section sekarang menampilkan data yang sebenarnya dari database
- Data akan otomatis update ketika ada event baru ditambahkan
- Data akan otomatis update ketika ada event yang dihapus

#### b. **Error Handling**
- Jika query database gagal, akan menggunakan fallback values (0)
- Error akan di-log ke error log PHP untuk troubleshooting
- Website tetap berfungsi meskipun ada masalah database

#### c. **Format Angka**
- Menggunakan `number_format()` untuk format angka yang lebih mudah dibaca
- Contoh: 1500 akan ditampilkan sebagai 1,500

### 5. **Struktur Database yang Digunakan**

#### Tabel `event`
- `id` - ID unik event
- `title` - Judul event
- `audience` - Jumlah peserta yang diharapkan
- `mitra` - Partner/mitra event
- `date` - Tanggal event
- `category` - Kategori event
- `type` - Tipe event

### 6. **Query Breakdown**

#### Query 1: Total Events
```sql
SELECT COUNT(*) as total FROM event
```
- **Tujuan**: Menghitung total event
- **Hasil**: Jumlah program pelatihan yang telah dibuat

#### Query 2: Total Audience
```sql
SELECT SUM(audience) as total FROM event WHERE audience IS NOT NULL
```
- **Tujuan**: Menjumlahkan semua audience
- **Filter**: Hanya event dengan audience yang valid
- **Hasil**: Total peserta dari semua event

#### Query 3: Total Partners
```sql
SELECT COUNT(DISTINCT mitra) as total FROM event WHERE mitra IS NOT NULL AND mitra != ''
```
- **Tujuan**: Menghitung mitra unik
- **Filter**: Hanya mitra yang tidak kosong
- **Hasil**: Total mitra strategis yang berbeda

## Keuntungan Integrasi Database

### 1. **Data Akurat**
- Menampilkan data yang sebenarnya dari database
- Tidak ada lagi data yang tidak akurat atau outdated

### 2. **Update Otomatis**
- Data stats akan otomatis update ketika ada perubahan di database
- Tidak perlu mengubah kode HTML untuk update data

### 3. **Konsistensi Data**
- Data stats section sama dengan data di admin panel
- Tidak ada perbedaan antara yang ditampilkan dan yang tersimpan

### 4. **Maintenance Mudah**
- Admin hanya perlu menambah/edit event di admin panel
- Stats section akan otomatis menampilkan data terbaru

## Cara Kerja

### 1. **Saat Halaman Dimuat**
1. PHP melakukan koneksi ke database
2. Menjalankan 3 query untuk mendapatkan statistik
3. Menyimpan hasil dalam variabel PHP
4. Menampilkan data dalam HTML

### 2. **Update Data**
1. Admin menambah/edit event di admin panel
2. Data tersimpan di database
3. Saat halaman utama di-refresh, stats section akan menampilkan data terbaru

### 3. **Error Handling**
1. Jika query berhasil, data ditampilkan
2. Jika query gagal, fallback values (0) ditampilkan
3. Error di-log untuk troubleshooting

## Testing

### 1. **Test Database Connection**
```bash
php Admin/test_connection.php
```

### 2. **Test Stats Section**
1. Buka `index.php` di browser
2. Scroll ke bagian stats section
3. Pastikan angka yang ditampilkan sesuai dengan data di database

### 3. **Test Update Data**
1. Login ke admin panel
2. Tambah event baru dengan audience dan mitra
3. Refresh halaman utama
4. Pastikan stats section menampilkan angka yang updated

## Monitoring

### 1. **Database Performance**
- Query stats section berjalan setiap kali halaman dimuat
- Untuk performa lebih baik, bisa ditambahkan caching

### 2. **Error Logging**
- Error database akan di-log ke error log PHP
- Cek error log untuk troubleshooting

### 3. **Data Accuracy**
- Pastikan field `audience` dan `mitra` terisi dengan benar
- Data NULL atau kosong akan mempengaruhi perhitungan

## Customization

### 1. **Mengubah Query**
Edit query sesuai kebutuhan:
```php
// Contoh: Hanya event yang sudah lewat
$stmt = $pdo->query("SELECT COUNT(*) as total FROM event WHERE date < CURDATE()");
```

### 2. **Mengubah Format Angka**
Edit format output:
```php
// Tanpa number_format
echo $total_audience;

// Dengan number_format
echo number_format($total_audience);
```

### 3. **Menambah Statistik Baru**
Tambahkan query dan variabel baru:
```php
// Contoh: Total event online
$stmt = $pdo->query("SELECT COUNT(*) as total FROM event WHERE type = 'online'");
$total_online_events = $stmt->fetch()['total'];
```

## Troubleshooting

### 1. **Stats Section Menampilkan 0**
- Cek apakah ada data event di database
- Cek koneksi database
- Cek error log PHP

### 2. **Data Tidak Update**
- Pastikan event baru tersimpan dengan benar
- Cek field `audience` dan `mitra` terisi
- Refresh halaman utama

### 3. **Error Database**
- Cek konfigurasi database
- Cek apakah tabel `event` ada
- Cek permission database user

## File yang Dimodifikasi

- `index.php` - Menambahkan query database untuk stats section
- `Admin/config/database.php` - Koneksi database (sudah ada)

## Catatan Penting

1. **Pastikan Database Terhubung**: File `Admin/config/database.php` harus dikonfigurasi dengan benar
2. **Data Event**: Pastikan ada data event di database untuk menampilkan statistik
3. **Field Audience**: Field `audience` harus berisi angka untuk perhitungan yang akurat
4. **Field Mitra**: Field `mitra` harus berisi string untuk perhitungan partner unik
5. **Performance**: Query berjalan setiap kali halaman dimuat, pertimbangkan caching untuk performa

---

**Dibuat oleh:** AI Assistant  
**Tanggal:** January 2025  
**Versi:** 1.0
