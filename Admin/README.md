# Admin Panel idSpora

Sistem administrasi untuk mengelola konten website idSpora dengan style yang konsisten dengan halaman user.

## Fitur Utama

### 🔐 Autentikasi
- Login admin dengan username dan password
- Session management untuk keamanan
- Logout otomatis

### 📊 Dashboard
- Statistik total events, mentors, news, dan reviews
- Quick actions untuk menambah konten baru
- Recent activities dari events dan mentors

### 📅 Manajemen Events
- Lihat semua events dengan informasi lengkap
- Tambah event baru dengan form yang lengkap
- Edit dan hapus events
- Filter berdasarkan kategori dan status

### 👥 Manajemen Mentors
- Lihat semua mentors dengan foto profil dan informasi
- Tambah mentor baru dengan data lengkap
- Upload foto profil wajib (JPG, PNG, GIF, WEBP)
- Upload file CV secara otomatis (PDF, DOC, DOCX)
- Link ke LinkedIn dan CV
- Edit dan hapus mentors
- Sistem upload file dengan validasi dan preview

### 📰 Manajemen News
- Lihat semua artikel berita
- Tambah artikel baru dengan editor
- Preview konten artikel
- Edit dan hapus berita

### ⭐ Manajemen Reviews
- Lihat semua review dengan rating bintang
- Tambah review baru
- Link review ke event tertentu
- Edit dan hapus reviews

## Struktur Database

Sistem menggunakan database MySQL dengan tabel-tabel berikut:

### Tabel `event`
- `id` - Primary key
- `title` - Judul event
- `description` - Deskripsi event
- `audience` - Jumlah peserta yang diharapkan
- `mitra` - Partner/mitra event
- `id_trainer` - Foreign key ke tabel mentor
- `category` - Kategori event (webinar & seminar, training & mini workshop, e-learning, video production)
- `type` - Tipe event (offline, online, hybrid)
- `date` - Tanggal event
- `venue` - Lokasi event

### Tabel `mentor`
- `id` - Primary key
- `name` - Nama mentor
- `description` - Deskripsi/jabatan mentor
- `linkedin` - URL LinkedIn
- `email` - Email mentor
- `cv` - Nama file CV

### Tabel `news`
- `id` - Primary key
- `title` - Judul artikel
- `article` - Konten artikel

### Tabel `review`
- `id` - Primary key
- `name` - Nama reviewer
- `job_title` - Jabatan reviewer
- `rate` - Rating (1-5)
- `review` - Isi review
- `id_event` - Foreign key ke tabel event

## Setup dan Instalasi

### 1. Setup Database
1. Import database `company_profil_idspora` ke MySQL server
2. Update konfigurasi database di `config/database.php`:
   ```php
   $host = 'localhost';
   $dbname = 'company_profil_idspora';
   $username = 'root';
   $password = '';
   ```

### 2. Test Koneksi Database
Akses `http://localhost/your-project/Admin/test_connection.php` untuk mengecek:
- Status koneksi database
- Tabel yang tersedia
- Sample data

### 3. Keamanan
- Folder `config/` dilindungi dengan `.htaccess`
- File `database.php` tidak boleh diakses langsung dari browser
- Gunakan `database.example.php` sebagai template untuk production

## Cara Penggunaan

### 1. Login Admin
```
Username: admin
Password: admin123
```

### 2. Akses Admin Panel
Buka browser dan akses: `http://localhost/your-project/Admin/login.php`

### 3. Navigasi
- **Dashboard**: Lihat statistik dan quick actions
- **Events**: Kelola semua events
- **Mentors**: Kelola semua mentors
- **News**: Kelola artikel berita
- **Reviews**: Kelola review dari peserta

### 4. Menambah Konten
- Klik tombol "Add New" di halaman yang sesuai
- Isi form dengan data yang diperlukan
- **Untuk Mentor**: Upload file CV (PDF/DOC/DOCX) atau biarkan kosong
- Klik "Save" untuk menyimpan

### 5. Upload File
- **Foto Profil (Wajib)**:
  - Format: JPG, JPEG, PNG, GIF, WEBP
  - Ukuran: Maksimal 5MB
  - Lokasi: `uploads/profile/`
  - Nama File: `profile_nama_mentor_timestamp.ext`
  - Preview: Tampil sebelum upload
  
- **CV File (Opsional)**:
  - Format: PDF, DOC, DOCX
  - Ukuran: Maksimal 5MB
  - Lokasi: `uploads/cv/`
  - Nama File: `cv_nama_mentor_timestamp.ext`
  - Validasi: Tipe file dan ukuran divalidasi otomatis

### 5. Mengedit Konten
- Klik ikon edit (pensil) pada item yang ingin diedit
- Modifikasi data yang diperlukan
- Klik "Update" untuk menyimpan perubahan

### 6. Menghapus Konten
- Klik ikon delete (tempat sampah) pada item yang ingin dihapus
- Konfirmasi penghapusan
- Item akan dihapus secara permanen

## Keamanan

### Session Management
- Setiap halaman admin memerlukan login
- Session timeout otomatis
- Redirect ke login jika tidak terautentikasi

### Input Validation
- Validasi form input
- Sanitasi data untuk mencegah XSS
- Prepared statements untuk mencegah SQL injection

### File Upload
- **Upload Foto Profil Wajib**: Sistem upload foto profil mentor dengan preview
- **Upload CV Otomatis**: Sistem upload file CV mentor dengan drag & drop
- **Format Foto**: JPG, JPEG, PNG, GIF, WEBP
- **Format CV**: PDF, DOC, DOCX
- **Ukuran Maksimal**: 5MB per file
- **Validasi File**: Tipe file dan ukuran divalidasi otomatis
- **Preview File**: Preview file yang dipilih sebelum upload
- **Penyimpanan Aman**: 
  - Foto profil disimpan di folder `uploads/profile/`
  - CV disimpan di folder `uploads/cv/`
- **Penggantian File**: File lama otomatis dihapus saat upload file baru

## Style dan UI

### Konsistensi Design
- Menggunakan CSS variables yang sama dengan halaman user
- Warna utama: Orange (#FF6B35) dan Dark Blue (#2C3E50)
- Font family: Segoe UI
- Border radius: 15-20px untuk modern look

### Responsive Design
- Bootstrap 5 untuk layout responsive
- Mobile-friendly interface
- Flexible grid system

### User Experience
- Loading states dan feedback
- Confirmation dialogs untuk aksi penting
- Success/error messages yang jelas
- Hover effects dan transitions

## File Structure

```
Admin/
├── config/
│   ├── database.php           # File connection database
│   ├── database.example.php   # Contoh konfigurasi database
│   └── .htaccess              # Keamanan folder config
├── index.php                  # Dashboard utama
├── login.php                  # Halaman login
├── logout.php                 # Proses logout
├── test_connection.php        # Test koneksi database
├── events.php                 # Manajemen events
├── add_event.php              # Form tambah event
├── mentors.php                # Manajemen mentors
├── add_mentor.php             # Form tambah mentor dengan upload CV
├── edit_mentor.php            # Form edit mentor dengan upload CV
├── news.php                   # Manajemen news
├── add_news.php               # Form tambah news
├── reviews.php                # Manajemen reviews
├── add_review.php             # Form tambah review
└── README.md                  # Dokumentasi ini

uploads/
├── profile/                   # Foto profil mentor
└── cv/                       # File CV mentor
```

## Konfigurasi Database

### File Connection Database
Sistem menggunakan file connection database terpusat di `Admin/config/database.php` yang berisi:

- Konfigurasi koneksi database
- Fungsi helper untuk koneksi
- Fungsi untuk mendapatkan statistik database
- Fungsi untuk mengecek struktur tabel

### Konfigurasi Default
File `config/database.php` berisi konfigurasi default:

```php
$host = 'localhost';
$dbname = 'company_profil_idspora';
$username = 'root';
$password = '';
```

### Cara Menggunakan
Setiap file admin menggunakan connection database dengan cara:

```php
// Include database connection
require_once 'config/database.php';

// Get database connection
$pdo = getDBConnection();

// Get database statistics
$stats = getDatabaseStats();
```

### Test Koneksi Database
Untuk mengetes koneksi database, akses:
`http://localhost/your-project/Admin/test_connection.php`

File ini akan menampilkan:
- Status koneksi database
- Statistik tabel
- Struktur tabel yang tersedia
- Sample data dari database

## Troubleshooting

### Error Koneksi Database
- Pastikan MySQL server berjalan
- Periksa kredensial database di `config/database.php`
- Pastikan database `company_profil_idspora` sudah dibuat
- Jalankan `test_connection.php` untuk mengecek status koneksi
- Periksa error log PHP untuk detail error

### Error Upload File
- Periksa permission folder `uploads/profile/` dan `uploads/cv/`
- Pastikan folder uploads sudah dibuat dengan permission yang benar
- Pastikan ukuran file tidak melebihi limit
- Validasi tipe file yang diizinkan

### Session Error
- Pastikan session sudah di-start di setiap halaman
- Periksa konfigurasi PHP session
- Clear browser cache jika diperlukan

## Pengembangan Selanjutnya

### Fitur yang Bisa Ditambahkan
- [ ] Upload gambar untuk events
- [ ] Rich text editor untuk news
- [ ] Export data ke Excel/PDF
- [ ] Backup database otomatis
- [ ] Multi-user admin dengan role berbeda
- [ ] Activity log untuk tracking perubahan
- [ ] Email notification untuk events baru
- [ ] Dashboard analytics yang lebih detail

### Optimasi
- [ ] Caching untuk performa
- [ ] Pagination untuk data besar
- [ ] Search dan filter advanced
- [ ] Bulk operations (delete multiple items)
- [ ] Image optimization

## Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development idSpora. 