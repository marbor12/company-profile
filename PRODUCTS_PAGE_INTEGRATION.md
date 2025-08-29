# Products Page Database Integration - idSpora

## 🎯 Overview
Halaman products.php telah berhasil diintegrasikan dengan database untuk menampilkan event yang sebenarnya dalam format carousel yang interaktif. Halaman ini sekarang menampilkan 6 event terbaru yang telah dilaksanakan dengan data real-time dari database.

## ✨ Fitur yang Telah Diimplementasikan

### 1. **Data Dinamis dari Database**
- Products page mengambil data langsung dari tabel `event` dan `documentation`
- Hanya menampilkan event yang sudah lewat (past events)
- Maksimal 6 event terbaru yang ditampilkan
- Urutan berdasarkan tanggal terbaru

### 2. **Carousel Interaktif**
- **Card Center**: Event yang sedang aktif dengan detail lengkap
- **Card Side**: Event di samping dengan informasi singkat
- **Card Hidden**: Event yang tersembunyi
- Navigasi dengan tombol prev/next dan swipe gesture
- Animasi smooth saat pergantian card

### 3. **Informasi Event Lengkap**
- **Judul Event**: Dari field `title` di database (main-title)
- **Kategori**: Dari field `category` di database (subtitle)
- **Tanggal**: Format Indonesia (contoh: "15 Januari 2025")
- **Deskripsi**: Dari field `description` di database
- **Fitur**: Jumlah peserta, tipe event, dan lokasi

### 4. **Gambar Dokumentasi Event**
- Menggunakan gambar dokumentasi yang diupload melalui admin panel
- Fallback ke gambar default (`property/training.jpg`) jika tidak ada dokumentasi
- Error handling untuk gambar yang tidak dapat dimuat
- Multiple images per event (menggunakan gambar pertama)

### 5. **Responsive Design**
- **Desktop**: Layout grid dengan carousel di sebelah kanan
- **Tablet**: Layout single column dengan carousel di bawah
- **Mobile**: Carousel scrollable horizontal dengan touch gesture
- Text wrapping dan truncation untuk teks panjang

## 🛠️ Cara Menggunakan

### Untuk Admin

#### 1. **Menambah Event Baru**
1. Login ke admin panel (`Admin/login.php`)
2. Buka menu "Events"
3. Klik "Add New Event"
4. Isi form dengan data lengkap:
   - Title: Judul event (akan menjadi main-title)
   - Description: Deskripsi event (akan ditampilkan di card)
   - Category: Kategori event (akan menjadi subtitle)
   - Audience: Jumlah peserta
   - Type: Tipe event (offline/online/hybrid)
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
- Event yang sudah lewat akan otomatis muncul di carousel
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
        e.description,
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
- `products.php` - Integrasi database dan carousel logic
- `Admin/config/database.php` - Koneksi database (sudah ada)
- `Admin/events.php` - Manajemen event (sudah ada)
- `Admin/documentation.php` - Manajemen dokumentasi (sudah ada)

## 📁 Struktur File

```
company-profile project awan/
├── products.php                        # Halaman portfolio dengan event carousel
├── Admin/
│   ├── config/
│   │   └── database.php                # Koneksi database
│   ├── events.php                      # Manajemen event
│   ├── add_event.php                   # Form tambah event
│   ├── documentation.php               # Manajemen dokumentasi
│   └── add_documentation.php           # Form tambah dokumentasi
├── uploads/
│   └── documentation/                  # Folder gambar dokumentasi
└── property/
    └── training.jpg                    # Gambar default fallback
```

## 🎨 Customization

### 1. **Mengubah Jumlah Event yang Ditampilkan**
Edit query di `products.php`:
```php
LIMIT 6  // Ubah angka sesuai kebutuhan
```

### 2. **Mengubah Styling Card**
Edit CSS di `products.php`:
```css
.nft-card {
    /* Customize card appearance */
}

.nft-title {
    /* Customize title styling */
}

.nft-description {
    /* Customize description styling */
}
```

### 3. **Mengubah Format Tanggal**
Edit JavaScript di `products.php`:
```javascript
const formattedDate = eventDate.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
});
```

## 🔧 Error Handling

### 1. **Database Connection Error**
- Fallback ke data default jika koneksi database gagal
- Pesan error yang informatif untuk user

### 2. **Query Error**
- Fallback ke data default jika query gagal
- Error logging untuk troubleshooting

### 3. **Image Loading Error**
- Fallback ke gambar default jika dokumentasi tidak dapat dimuat
- `onerror` handler pada tag img

### 4. **Empty Data**
- Fallback ke data default jika tidak ada event
- Pesan yang informatif untuk user

## 📱 Responsive Features

### 1. **Desktop (992px+)**
- Layout grid 2 kolom
- Carousel dengan 3 card visible
- Full description display

### 2. **Tablet (768px - 991px)**
- Layout single column
- Carousel dengan 2 card visible
- Truncated description

### 3. **Mobile (< 768px)**
- Horizontal scrollable carousel
- Touch gesture support
- Compact card design
- Minimal description

## 🚀 Performance Optimizations

### 1. **Image Optimization**
- Lazy loading untuk gambar
- Fallback images untuk error handling
- Optimized image paths

### 2. **Database Optimization**
- Single query untuk semua data
- Proper indexing on date field
- Limited results (6 events)

### 3. **JavaScript Optimization**
- Efficient DOM manipulation
- Smooth animations with CSS transitions
- Event delegation for better performance

## 🔄 Update Process

### 1. **Automatic Updates**
- Data akan otomatis update ketika ada event baru
- Data akan otomatis update ketika ada dokumentasi baru
- No manual refresh required

### 2. **Manual Refresh**
- User dapat refresh halaman untuk mendapatkan data terbaru
- Carousel akan reset ke event pertama

## 📊 Data Flow

```
Database (event + documentation)
    ↓
PHP Query & Processing
    ↓
JSON Data to JavaScript
    ↓
Carousel Rendering
    ↓
User Interaction
    ↓
Content Updates
```

## 🎯 Future Enhancements

### 1. **Filtering Options**
- Filter by category
- Filter by date range
- Search functionality

### 2. **Additional Features**
- Event details modal
- Share functionality
- Download documentation

### 3. **Performance Improvements**
- Image preloading
- Caching mechanisms
- CDN integration

---

**Note**: Pastikan database connection dan folder permissions sudah dikonfigurasi dengan benar sebelum menggunakan fitur ini.
