# Experts Section Database Integration

## Overview
Bagian experts/mentor section pada halaman utama (`index.php`) telah diintegrasikan dengan database untuk menampilkan data mentor yang sebenarnya dari tabel `mentor`. Sekarang experts section menampilkan data real-time dari database, termasuk foto profil, nama mentor, deskripsi, dan link LinkedIn.

## Perubahan yang Dilakukan

### 1. **Modifikasi `index.php`**
- Menambahkan query database untuk mengambil data mentor
- Mengganti hardcoded mentor cards dengan data dinamis dari database
- Menambahkan logika untuk menampilkan foto profil, nama, deskripsi, dan LinkedIn
- Mempertahankan animasi carousel dan styling yang sama

### 2. **Data yang Diambil dari Database**

#### Tabel `mentor`
- `id` - ID unik mentor
- `name` - Nama lengkap mentor
- `description` - Deskripsi/jabatan mentor
- `linkedin` - URL LinkedIn mentor
- `profile_pict` - Nama file foto profil

### 3. **Query Database yang Digunakan**
```php
$stmt = $pdo->query("
    SELECT id, name, description, linkedin, profile_pict 
    FROM mentor 
    ORDER BY name ASC
");
$mentors = $stmt->fetchAll();
```

### 4. **Fitur yang Diimplementasikan**

#### a. **Data Real-time dari Database**
- Experts section sekarang menampilkan data mentor yang sebenarnya dari database
- Data akan otomatis update ketika ada mentor baru ditambahkan
- Data akan otomatis update ketika ada mentor yang dihapus

#### b. **Foto Profil Mentor**
- Menggunakan foto profil yang diupload melalui admin panel
- Fallback ke gambar default (`property/profile.png`) jika tidak ada foto profil
- Error handling untuk gambar yang tidak dapat dimuat

#### c. **Informasi Mentor Lengkap**
- **Nama Mentor**: Dari field `name` di database
- **Deskripsi**: Dari field `description` (jika tersedia)
- **LinkedIn**: Link ke profil LinkedIn mentor (jika tersedia)

#### d. **URL Generation Otomatis**
- URL mentor page dibuat otomatis berdasarkan nama mentor
- Format: `mentor-{nama_mentor}.html`
- Contoh: `mentor-awan.html`, `mentor-sriwidianingsih.html`

#### e. **Carousel Animation**
- Mempertahankan animasi carousel yang sama persis
- Duplikasi cards untuk seamless loop
- Hover effects dan styling yang konsisten

### 5. **Implementasi Kode**

#### a. **Query Database**
```php
// Get mentors data for experts section
try {
    $stmt = $pdo->query("
        SELECT id, name, description, linkedin, profile_pict 
        FROM mentor 
        ORDER BY name ASC
    ");
    $mentors = $stmt->fetchAll();
} catch(PDOException $e) {
    $mentors = [];
    error_log("Error getting mentors: " . $e->getMessage());
}
```

#### b. **HTML Output Dinamis**
```php
<?php if (empty($mentors)): ?>
  <div class="text-center">
    <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
    <p class="text-muted">Belum ada mentor yang tersedia.</p>
  </div>
<?php else: ?>
  <div class="experts-carousel-container" data-aos="fade-up">
    <div class="experts-carousel-track">
      <?php foreach ($mentors as $mentor): ?>
        <div class="experts-item-wrapper">
          <a href="mentor-<?php echo strtolower(str_replace([' ', '.'], ['', ''], $mentor['name'])); ?>.html">
            <div class="agent-card">
              <img src="<?php echo !empty($mentor['profile_pict']) ? 'uploads/profile/' . htmlspecialchars($mentor['profile_pict']) : 'property/profile.png'; ?>" 
                   class="agent-photo mb-3" 
                   alt="<?php echo htmlspecialchars($mentor['name']); ?>"
                   onerror="this.src='property/profile.png'" />
              <h5><?php echo htmlspecialchars($mentor['name']); ?></h5>
              <?php if (!empty($mentor['description'])): ?>
                <small class="text-muted"><?php echo htmlspecialchars($mentor['description']); ?></small>
              <?php endif; ?>
            </div>
            <div class="social-links">
              <?php if (!empty($mentor['linkedin'])): ?>
                <a href="<?php echo htmlspecialchars($mentor['linkedin']); ?>" target="_blank" onclick="event.stopPropagation()">
                  <i class="fab fa-linkedin"></i>
                </a>
              <?php endif; ?>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
      
      <!-- Duplicate cards for seamless loop -->
      <?php foreach ($mentors as $mentor): ?>
        <!-- Same structure as above -->
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
```

### 6. **Struktur HTML yang Dihasilkan**

Setiap mentor card menampilkan:
- **Foto Profil**: Dari `uploads/profile/` atau fallback image
- **Nama Mentor**: Dari field `name` di database
- **Deskripsi**: Dari field `description` (jika tersedia)
- **LinkedIn Icon**: Link ke profil LinkedIn (jika tersedia)
- **Link ke Halaman Mentor**: URL otomatis berdasarkan nama

### 7. **Fallback dan Error Handling**

#### Jika Tidak Ada Mentor
```php
<?php if (empty($mentors)): ?>
  <div class="text-center">
    <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
    <p class="text-muted">Belum ada mentor yang tersedia.</p>
  </div>
<?php endif; ?>
```

#### Jika Foto Tidak Dapat Dimuat
```html
<img src="<?php echo !empty($mentor['profile_pict']) ? 'uploads/profile/' . htmlspecialchars($mentor['profile_pict']) : 'property/profile.png'; ?>" 
     class="agent-photo mb-3" 
     alt="<?php echo htmlspecialchars($mentor['name']); ?>"
     onerror="this.src='property/profile.png'" />
```

## Cara Menambahkan Mentor

### 1. **Tambah Mentor Baru**
1. Login ke admin panel
2. Buka menu "Mentors"
3. Klik "Add New Mentor"
4. Isi form dengan data lengkap:
   - Name: Nama lengkap mentor
   - Description: Deskripsi/jabatan mentor
   - LinkedIn: URL profil LinkedIn
   - Upload foto profil (wajib)
5. Simpan mentor

### 2. **Mentor Akan Muncul Otomatis**
- Mentor baru akan otomatis muncul di experts section
- Urutan berdasarkan nama (A-Z)
- Foto profil akan ditampilkan jika tersedia

### 3. **Update Data Mentor**
- Edit mentor di admin panel
- Perubahan akan otomatis terlihat di experts section
- Tidak perlu mengubah kode HTML

## Keuntungan Integrasi Database

### 1. **Data Real-time**
- Experts section selalu menampilkan data mentor terbaru
- Tidak ada lagi data yang tidak akurat atau outdated

### 2. **Update Otomatis**
- Data mentor akan otomatis update ketika ada perubahan
- Tidak perlu mengubah kode HTML untuk update data

### 3. **Konsistensi Data**
- Data experts section sama dengan data di admin panel
- Tidak ada perbedaan antara yang ditampilkan dan yang tersimpan

### 4. **Maintenance Mudah**
- Admin dapat mengelola mentor melalui panel admin
- Experts section akan otomatis menampilkan data terbaru

### 5. **Skalabilitas**
- Mudah menambah mentor baru tanpa mengubah kode
- Tidak ada batasan jumlah mentor yang ditampilkan

## Cara Kerja

### 1. **Saat Halaman Dimuat**
1. PHP melakukan koneksi ke database
2. Menjalankan query untuk mendapatkan data mentor
3. Menyimpan hasil dalam array `$mentors`
4. Menampilkan mentor cards dalam HTML

### 2. **Update Data**
1. Admin menambah/edit mentor di admin panel
2. Data tersimpan di database
3. Saat halaman utama di-refresh, experts section akan menampilkan data terbaru

### 3. **Error Handling**
1. Jika query berhasil, mentor cards ditampilkan
2. Jika query gagal, pesan "Belum ada mentor yang tersedia" ditampilkan
3. Error di-log untuk troubleshooting

## Testing

### 1. **Test Database Connection**
```bash
php Admin/test_connection.php
```

### 2. **Test Experts Section**
1. Buka `index.php` di browser
2. Scroll ke bagian "Mentor Kami"
3. Pastikan mentor cards menampilkan data dari database

### 3. **Test Update Data**
1. Login ke admin panel
2. Tambah mentor baru dengan foto profil dan LinkedIn
3. Refresh halaman utama
4. Pastikan mentor baru muncul di experts section

## Monitoring

### 1. **Database Performance**
- Query mentor berjalan setiap kali halaman dimuat
- Untuk performa lebih baik, bisa ditambahkan caching

### 2. **Error Logging**
- Error database akan di-log ke error log PHP
- Cek error log untuk troubleshooting

### 3. **Data Accuracy**
- Pastikan field `name`, `description`, dan `linkedin` terisi dengan benar
- Pastikan foto profil tersimpan di folder `uploads/profile/`

## Customization

### 1. **Mengubah Query**
Edit query sesuai kebutuhan:
```php
// Contoh: Hanya mentor dengan foto profil
$stmt = $pdo->query("
    SELECT id, name, description, linkedin, profile_pict 
    FROM mentor 
    WHERE profile_pict IS NOT NULL
    ORDER BY name ASC
");
```

### 2. **Mengubah URL Format**
Edit format URL mentor:
```php
// Format saat ini
mentor-<?php echo strtolower(str_replace([' ', '.'], ['', ''], $mentor['name'])); ?>.html

// Format alternatif
mentor-<?php echo $mentor['id']; ?>.html
```

### 3. **Menambah Field Baru**
Tambahkan field baru ke query:
```php
$stmt = $pdo->query("
    SELECT id, name, description, linkedin, profile_pict, email, phone
    FROM mentor 
    ORDER BY name ASC
");
```

## Troubleshooting

### 1. **Experts Section Kosong**
- Cek apakah ada data mentor di database
- Cek koneksi database
- Cek error log PHP

### 2. **Foto Mentor Tidak Muncul**
- Cek path folder `uploads/profile/`
- Pastikan permission folder benar
- Cek apakah file foto ada di folder

### 3. **LinkedIn Link Tidak Berfungsi**
- Pastikan field `linkedin` berisi URL yang valid
- Cek apakah URL dapat diakses

### 4. **Error Database**
- Cek konfigurasi database di `Admin/config/database.php`
- Pastikan tabel `mentor` ada
- Cek permission database user

## File yang Dimodifikasi

- `index.php` - Menambahkan query database untuk experts section
- `Admin/config/database.php` - Koneksi database (sudah ada)
- `Admin/mentors.php` - Manajemen mentor (sudah ada)

## Catatan Penting

1. **Pastikan Database Terhubung**: File `Admin/config/database.php` harus dikonfigurasi dengan benar
2. **Data Mentor**: Pastikan ada data mentor di database untuk menampilkan experts section
3. **Foto Profil**: Field `profile_pict` harus berisi nama file yang valid
4. **LinkedIn**: Field `linkedin` harus berisi URL yang valid
5. **Folder Uploads**: Pastikan folder `uploads/profile/` memiliki permission yang tepat
6. **Gambar Default**: File `property/profile.png` harus tersedia sebagai fallback

## Status Implementasi

- ✅ Query database untuk mentor berfungsi
- ✅ Data mentor ditampilkan secara dinamis
- ✅ Foto profil mentor dengan fallback image
- ✅ Link LinkedIn mentor
- ✅ URL mentor page otomatis
- ✅ Carousel animation tetap berfungsi
- ✅ Error handling dan fallback
- ✅ Styling dan layout konsisten

---

**Dibuat oleh:** AI Assistant  
**Tanggal:** January 2025  
**Versi:** 1.0
