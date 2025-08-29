# Event Gallery Integration - idSpora

## 🎯 Overview
Event gallery section pada halaman utama website idSpora telah berhasil diintegrasikan dengan database. Sekarang event gallery menampilkan data real-time dari database, termasuk gambar dokumentasi event, nama event, tanggal pelaksanaan, dan jumlah peserta.

## ✨ Fitur yang Telah Diimplementasikan

### 1. **Data Dinamis dari Database**
- Event gallery mengambil data langsung dari tabel `event` dan `documentation`
- Hanya menampilkan event yang sudah lewat (past events)
- Maksimal 6 event terbaru yang ditampilkan

### 2. **Gambar Dokumentasi Event**
- Menggunakan gambar dokumentasi yang diupload melalui admin panel
- Fallback ke gambar default jika tidak ada dokumentasi
- Error handling untuk gambar yang tidak dapat dimuat

### 3. **Informasi Event Lengkap**
- **Judul Event**: Dari field `title` di database
- **Tanggal**: Format Indonesia (contoh: "15 Januari 2025")
- **Jumlah Peserta**: Dari field `audience` dengan badge berwarna

### 4. **Badge Warna Dinamis**
Berdasarkan jumlah peserta:
- 🟢 **Hijau** (`bg-success`): < 20 peserta
- 🟡 **Kuning** (`bg-warning`): 20-49 peserta  
- 🔵 **Biru** (`bg-info`): 50-99 peserta
- 🔴 **Merah** (`bg-danger`): 100-499 peserta
- ⚫ **Abu-abu** (`bg-secondary`): ≥ 500 peserta

## 🛠️ Cara Menggunakan

### Untuk Admin

#### 1. **Menambah Event Baru**
1. Login ke admin panel (`Admin/login.php`)
2. Buka menu "Events"
3. Klik "Add New Event"
4. Isi form dengan data lengkap:
   - Title: Judul event
   - Description: Deskripsi event
   - Audience: Jumlah peserta yang diharapkan
   - Mitra: Partner/mitra event
   - Trainer: Pilih mentor dari dropdown
   - Category: Pilih kategori event
   - Type: Pilih tipe event (offline/online/hybrid)
   - Date: Tanggal pelaksanaan
   - Venue: Lokasi event
5. Klik "Save Event"

#### 2. **Menambah Dokumentasi Event**
1. Setelah event selesai, buka menu "Documentation"
2. Klik "Add New Documentation"
3. Pilih event yang sudah lewat dari dropdown
4. Upload gambar dokumentasi (JPG, PNG, GIF, max 5MB)
5. Klik "Save Documentation"

#### 3. **Event Akan Muncul Otomatis**
- Event yang sudah lewat akan otomatis muncul di gallery
- Urutan berdasarkan tanggal terbaru
- Maksimal 6 event yang ditampilkan

### Untuk Developer

#### 1. **Struktur Database**
```sql
-- Tabel event
CREATE TABLE event (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    audience INT,
    mitra VARCHAR(255),
    id_trainer INT,
    category VARCHAR(100),
    type VARCHAR(50),
    date DATE,
    venue VARCHAR(255)
);

-- Tabel documentation
CREATE TABLE documentation (
    id_dokumentasi INT PRIMARY KEY AUTO_INCREMENT,
    picture VARCHAR(255),
    id_event INT,
    FOREIGN KEY (id_event) REFERENCES event(id)
);
```

#### 2. **Query yang Digunakan**
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

#### 3. **File yang Dimodifikasi**
- `index.php` - Menambahkan koneksi database dan logika event gallery
- `Admin/config/database.php` - Koneksi database (sudah ada)
- `Admin/events.php` - Manajemen event (sudah ada)
- `Admin/documentation.php` - Manajemen dokumentasi (sudah ada)

## 📁 Struktur File

```
company-profile project awan/
├── index.php                          # Halaman utama dengan event gallery
├── Admin/
│   ├── config/
│   │   └── database.php               # Koneksi database
│   ├── events.php                     # Manajemen event
│   ├── add_event.php                  # Form tambah event
│   ├── documentation.php              # Manajemen dokumentasi
│   └── add_documentation.php          # Form tambah dokumentasi
├── uploads/
│   └── documentation/                 # Folder gambar dokumentasi
└── property/
    └── training.jpg                   # Gambar default fallback
```

## 🔧 Konfigurasi

### 1. **Database Connection**
Pastikan file `Admin/config/database.php` dikonfigurasi dengan benar:
```php
$host = 'localhost';
$dbname = 'company_profil_idspora';
$username = 'root';
$password = '';
```

### 2. **Folder Permissions**
Pastikan folder `uploads/documentation/` memiliki permission yang tepat:
```bash
chmod 755 uploads/documentation/
```

### 3. **Gambar Default**
Pastikan file `property/training.jpg` tersedia sebagai fallback image.

## 🎨 Customization

### 1. **Mengubah Jumlah Event yang Ditampilkan**
Edit query di `index.php`:
```php
LIMIT 6  // Ubah angka sesuai kebutuhan
```

### 2. **Mengubah Badge Colors**
Edit logika badge di `index.php`:
```php
if ($event['audience'] < 20) {
    $badgeClass = 'bg-success';
} elseif ($event['audience'] < 50) {
    $badgeClass = 'bg-warning';
}
// ... dan seterusnya
```

### 3. **Mengubah Format Tanggal**
Edit format tanggal di `index.php`:
```php
$formattedDate = $eventDate->format('d F Y'); // Format saat ini
// Ubah menjadi format yang diinginkan
```

## 🚀 Testing

### 1. **Test Koneksi Database**
```bash
php Admin/test_connection.php
```

### 2. **Test Event Gallery**
1. Buka `index.php` di browser
2. Scroll ke bagian "Dokumentasi Event Kami"
3. Pastikan event gallery menampilkan data dari database

### 3. **Test Admin Panel**
1. Login ke admin panel
2. Tambah event baru
3. Tambah dokumentasi untuk event tersebut
4. Refresh halaman utama untuk melihat perubahan

## 📊 Monitoring

### 1. **Log Error**
Error database akan di-log ke error log PHP. Cek error log untuk troubleshooting.

### 2. **Database Stats**
Gunakan `Admin/test_connection.php` untuk melihat statistik database.

### 3. **File Upload**
Pastikan folder `uploads/documentation/` memiliki space yang cukup.

## 🔒 Security

### 1. **SQL Injection Protection**
Menggunakan PDO prepared statements untuk semua query database.

### 2. **File Upload Security**
- Validasi tipe file (hanya gambar)
- Validasi ukuran file (max 5MB)
- Generate nama file unik

### 3. **XSS Protection**
Menggunakan `htmlspecialchars()` untuk output data ke HTML.

## 🐛 Troubleshooting

### 1. **Event Gallery Kosong**
- Pastikan ada event yang sudah lewat di database
- Cek koneksi database
- Pastikan query berjalan dengan benar

### 2. **Gambar Tidak Muncul**
- Cek path folder `uploads/documentation/`
- Pastikan permission folder benar
- Cek apakah file gambar ada di folder

### 3. **Error Database**
- Cek konfigurasi database di `Admin/config/database.php`
- Pastikan tabel `event` dan `documentation` ada
- Cek error log PHP

## 📈 Performance

### 1. **Optimization**
- Query menggunakan `LIMIT 6` untuk membatasi data
- Hanya mengambil event yang sudah lewat
- Menggunakan `LEFT JOIN` untuk efisiensi

### 2. **Caching**
Untuk performa lebih baik, bisa ditambahkan caching:
- File cache untuk data event
- Cache gambar dengan CDN
- Database query optimization

## 🔄 Maintenance

### 1. **Regular Cleanup**
- Hapus event lama yang tidak diperlukan
- Bersihkan gambar dokumentasi yang tidak digunakan
- Backup database secara berkala

### 2. **Updates**
- Update gambar default jika diperlukan
- Modifikasi query sesuai kebutuhan bisnis
- Update styling sesuai design system

## 📞 Support

Jika ada masalah atau pertanyaan:
1. Cek dokumentasi ini terlebih dahulu
2. Cek error log PHP
3. Test koneksi database
4. Hubungi tim development

---

**Dibuat oleh:** AI Assistant  
**Tanggal:** January 2025  
**Versi:** 1.0
