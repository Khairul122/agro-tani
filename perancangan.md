# RANCANG BANGUN SISTEM INFORMASI REKAP DATA PENJUALAN DAN STOK BARANG PADA TOKO GANI AGRO TANI BERBASIS WEB DENGAN MENGGUNAKAN BAHASA PEMROGRAMAN PHP DAN DATABASE MYSQL

## 1. Pendahuluan

### 1.1 Latar Belakang
Toko Gani Agro Tani merupakan toko yang bergerak di bidang pertanian yang menjual berbagai kebutuhan pertanian seperti pupuk, benih, pestisida, dan alat pertanian. Dalam operasional sehari-hari, toko ini melakukan kegiatan penjualan dan pembelian barang yang perlu dicatat dan dikelola dengan baik untuk mengetahui stok barang dan rekap data penjualan.

Saat ini, proses pengelolaan data penjualan dan stok barang masih dilakukan secara manual, sehingga menyulitkan dalam pencatatan, pencarian data, dan pengambilan keputusan. Oleh karena itu, perlu dibangun sistem informasi rekap data penjualan dan stok barang yang terkomputerisasi untuk meningkatkan efisiensi dan efektivitas dalam pengelolaan data.

### 1.2 Tujuan
1. Membangun sistem informasi rekap data penjualan dan stok barang berbasis web
2. Memudahkan pencatatan transaksi penjualan dan pembelian
3. Memudahkan dalam pengelolaan data barang, pelanggan, dan supplier
4. Menyediakan laporan data penjualan dan stok barang secara cepat dan akurat
5. Menghindari kesalahan dalam perhitungan stok dan total transaksi

### 1.3 Ruang Lingkup
1. Sistem hanya mencakup fitur untuk admin, kasir, dan owner
2. Meliputi manajemen data master (barang, kategori, pelanggan, supplier)
3. Meliputi transaksi penjualan dan pembelian
4. Meliputi laporan penjualan dan stok barang
5. Menggunakan struktur MVC (Model-View-Controller)

## 2. Struktur Database

### 2.1 Tabel-tabel dalam Database: toko_gani_agro_tani

#### 2.1.1 Tabel users
```sql
CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100),
  role ENUM('admin','kasir','owner') DEFAULT 'kasir',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2.1.2 Tabel kategori_barang
```sql
CREATE TABLE kategori_barang (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
);
```

#### 2.1.3 Tabel barang
```sql
CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT,
  kode_barang VARCHAR(30) UNIQUE,
  nama_barang VARCHAR(150) NOT NULL,
  satuan VARCHAR(50),
  harga_beli DECIMAL(15,2),
  harga_jual DECIMAL(15,2),
  stok INT DEFAULT 0,
  FOREIGN KEY (id_kategori) REFERENCES kategori_barang(id_kategori) ON DELETE SET NULL
);
```

#### 2.1.4 Tabel pelanggan
```sql
CREATE TABLE pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(150),
  alamat TEXT,
  no_hp VARCHAR(20)
);
```

#### 2.1.5 Tabel supplier
```sql
CREATE TABLE supplier (
  id_supplier INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier VARCHAR(150),
  alamat TEXT,
  no_hp VARCHAR(20)
);
```

#### 2.1.6 Tabel penjualan
```sql
CREATE TABLE penjualan (
  id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
  no_faktur VARCHAR(50) UNIQUE,
  tanggal DATE DEFAULT (CURRENT_DATE),
  id_pelanggan INT,
  total DECIMAL(15,2) DEFAULT 0,
  id_user INT,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);
```

#### 2.1.7 Tabel detail_penjualan
```sql
CREATE TABLE detail_penjualan (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_penjualan INT,
  id_barang INT,
  jumlah INT,
  harga_satuan DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);
```

#### 2.1.8 Tabel pembelian
```sql
CREATE TABLE pembelian (
  id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
  no_nota VARCHAR(50) UNIQUE,
  tanggal DATE DEFAULT (CURRENT_DATE),
  id_supplier INT,
  total DECIMAL(15,2) DEFAULT 0,
  id_user INT,
  FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier) ON DELETE SET NULL,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);
```

#### 2.1.9 Tabel detail_pembelian
```sql
CREATE TABLE detail_pembelian (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_pembelian INT,
  id_barang INT,
  jumlah INT,
  harga_beli DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  FOREIGN KEY (id_pembelian) REFERENCES pembelian(id_pembelian) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);
```

#### 2.1.10 Tabel aktivitas_user
```sql
CREATE TABLE aktivitas_user (
  id_log INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  aktivitas VARCHAR(255),
  waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
);
```

## 3. Struktur MVC (Model-View-Controller)

### 3.1 Struktur Folder
```
agro_tani/
├── config/
├── models/
├── views/
├── controllers/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── index.php
└── template.php
```

### 3.2 Penjelasan Struktur Folder
- **config/**: Berisi file konfigurasi aplikasi seperti koneksi database
  - **koneksi.php**: File ini berisi class Database yang mengelola koneksi ke database MySQL menggunakan PDO. File ini mendefinisikan host, username, password, dan nama database yang digunakan (dalam implementasi sebenarnya menggunakan database "agro_tano", tetapi seharusnya "toko_gani_agro_tani"). Menggunakan metode getConnection() untuk membuat koneksi PDO dan mengatur mode error serta fetch mode.
- **models/**: Berisi file-model yang mengelola data dari database
- **views/**: Berisi file tampilan yang ditampilkan ke pengguna
- **controllers/**: Berisi file controller yang mengelola logika aplikasi
  - Struktur penamaan: Nama file diawali huruf kapital mengikuti format {NamaController}Controller.php
  - Contoh: BarangController.php, PenjualanController.php
- **assets/**: Berisi file pendukung seperti CSS, JS, dan gambar
- **index.php**: File utama untuk menjalankan aplikasi
  - File ini berfungsi sebagai router utama (front controller) yang menentukan controller dan action mana yang akan dijalankan berdasarkan parameter GET.
  - Memulai session PHP untuk manajemen login
  - Memuat file koneksi database dan membuat instance Database
  - Menentukan controller dan action berdasarkan parameter URL (misalnya ?controller=barang&action=tambah)
  - Menginisialisasi controller dan menjalankan action yang sesuai
  - Jika controller atau action tidak ditemukan, menampilkan pesan kesalahan
- **template.php**: File template untuk menyatukan tampilan

### 3.3 Model
- **User_model.php**: Mengelola data user
- **Kategori_model.php**: Mengelola data kategori barang
- **Barang_model.php**: Mengelola data barang dan stok
- **Pelanggan_model.php**: Mengelola data pelanggan
- **Supplier_model.php**: Mengelola data supplier
- **Penjualan_model.php**: Mengelola data penjualan
- **Pembelian_model.php**: Mengelola data pembelian

### 3.4 View
- **login.php**: Tampilan halaman login
- **dashboard.php**: Tampilan dashboard untuk masing-masing role
- **barang/**: Tampilan untuk manajemen barang
- **penjualan/**: Tampilan untuk transaksi dan laporan penjualan
- **pembelian/**: Tampilan untuk transaksi dan laporan pembelian
- **laporan/**: Tampilan laporan rekap data
- **partials/**: Komponen tampilan yang digunakan bersama

### 3.5 Controller
- **AuthController.php**: Menangani proses login/logout
- **DashboardController.php**: Menampilkan dashboard berdasarkan role
- **BarangController.php**: Mengelola CRUD barang
- **KategoriController.php**: Mengelola CRUD kategori
- **PelangganController.php**: Mengelola CRUD pelanggan
- **SupplierController.php**: Mengelola CRUD supplier
- **PenjualanController.php**: Mengelola transaksi penjualan dan laporan
- **PembelianController.php**: Mengelola transaksi pembelian dan laporan

### 3.6 Implementasi MVC dalam Sistem
Berdasarkan file index.php, sistem ini menerapkan pola routing sederhana berbasis parameter GET:
- Format URL: `index.php?controller={nama_controller}&action={nama_method}`
- Controller diakses dengan penamaan `{NamaController}Controller.php` di folder controllers/
- Method dalam controller diakses sebagai action
- Jika controller tidak ditemukan, sistem menampilkan pesan "Controller not found"
- Jika action tidak ditemukan, sistem akan mencoba menjalankan method `index()` sebagai fallback
- Seluruh sistem menggunakan session untuk manajemen login dan role pengguna

## 4. Fitur-fitur Sistem

### 4.1 Fitur Login dan Otentikasi
- Sistem login multi-role (admin, kasir, owner)
- Validasi username dan password
- Session management
- Otentikasi berdasarkan role

### 4.2 Fitur Master Data
- **Manajemen Kategori Barang**: Tambah, edit, hapus kategori barang
- **Manajemen Barang**: Tambah, edit, hapus barang dengan stok otomatis
- **Manajemen Pelanggan**: Tambah, edit, hapus data pelanggan
- **Manajemen Supplier**: Tambah, edit, hapus data supplier

### 4.3 Fitur Transaksi
- **Transaksi Penjualan**: Membuat faktur penjualan dengan detail barang, jumlah, harga, dan subtotal
- **Transaksi Pembelian**: Membuat nota pembelian untuk menambah stok barang
- **Auto-update stok**: Stok barang otomatis berkurang/tambah saat transaksi

### 4.4 Fitur Laporan
- **Laporan Penjualan**: Berdasarkan periode, pelanggan, atau barang
- **Laporan Pembelian**: Berdasarkan periode atau supplier
- **Laporan Stok Barang**: Menampilkan stok saat ini, barang habis, barang minimum
- **Laporan Laba Rugi**: Perhitungan keuntungan dari transaksi penjualan

### 4.5 Fitur Manajemen Pengguna
- Manajemen akun user (admin, kasir, owner)
- Ganti password
- Log aktivitas user

### 4.6 Fitur Notifikasi
- Notifikasi stok barang rendah
- Notifikasi berhasil/hapus/gagal pada setiap aksi
- Notifikasi validasi form
- Implementasi notifikasi menggunakan JavaScript `window.alert()` untuk pesan kesalahan atau konfirmasi penting
- Penggunaan SweetAlert2 untuk notifikasi yang lebih menarik (jika tersedia)

## 5. Spesifikasi Teknis

### 5.1 Teknologi yang Digunakan
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Framework CSS**: Bootstrap 5
- **Arsitektur**: MVC (Model-View-Controller)
- **Server**: Apache (menggunakan Laragon/XAMPP)

### 5.2 Fitur Notifikasi Windows Alert
- Sistem notifikasi menggunakan `window.alert()` untuk pesan kesalahan
- Notifikasi sukses menggunakan SweetAlert2 atau sistem notifikasi bawaan
- Validasi form dilakukan di sisi client dan server
- Notifikasi stok rendah ditampilkan di dashboard
- Implementasi redirect dengan notifikasi dilakukan menggunakan JavaScript setelah proses di controller selesai

### 5.3 Keamanan
- Hash password menggunakan password_hash()
- Validasi data input
- CSRF protection
- SQL injection prevention dengan prepared statements

### 5.4 Responsive Design
- Tampilan yang responsif untuk berbagai ukuran perangkat
- Mudah digunakan di komputer dan perangkat mobile

## 6. Desain Antarmuka (UI/UX)

### 6.1 Halaman Login
- Form login dengan username dan password
- Validasi input
- Pilihan role

### 6.2 Dashboard
- Ringkasan data (total penjualan, stok barang, transaksi terakhir)
- Menu navigasi utama
- Tampilan berbeda untuk masing-masing role

### 6.3 Data Master
- Tabel data dengan fitur pencarian, penyortiran, dan pagination
- Form tambah dan edit data
- Konfirmasi sebelum hapus data

### 6.4 Transaksi
- Form transaksi dengan pemilihan barang dan perhitungan otomatis
- Preview faktur/nota sebelum disimpan
- Riwayat transaksi

### 6.5 Laporan
- Filter berdasarkan tanggal atau kriteria lain
- Cetak laporan dalam format PDF
- Ekspor ke Excel

## 7. Implementasi dan Jadwal

### 7.1 Tahapan Pengembangan
- **Tahap 1**: Persiapan (konfigurasi dan struktur dasar)
- **Tahap 2**: Implementasi login dan dashboard
- **Tahap 3**: Implementasi master data
- **Tahap 4**: Implementasi transaksi
- **Tahap 5**: Implementasi laporan
- **Tahap 6**: Testing dan debugging
- **Tahap 7**: Deployment

### 7.2 Jadwal Perkiraan
- Tahap 1-2: 1 minggu
- Tahap 3: 1 minggu
- Tahap 4: 1 minggu
- Tahap 5: 1 minggu
- Tahap 6-7: 1 minggu
- Total: 5 minggu

## 8. Kesimpulan
Sistem informasi rekap data penjualan dan stok barang ini akan membantu Toko Gani Agro Tani dalam mengelola data penjualan dan stok barang secara efektif dan efisien. Diharapkan sistem ini dapat meningkatkan produktivitas dan akurasi dalam pengelolaan data transaksi, serta memberikan kemudahan dalam pengambilan keputusan bisnis.

## 9. Harus Dilakukan dan Jangan Dilakukan

### 9.1 Harus Dilakukan
- Gunakan template frontend yang telah disediakan dalam pengembangan tampilan:
  ```php
  <?php include('template/header.php'); ?>
  
  <body class="with-welcome-text">
    <div class="container-scroller">
      <?php include 'template/navbar.php'; ?>
      <div class="container-fluid page-body-wrapper">
        <?php include 'template/setting_panel.php'; ?>
        <?php include 'template/sidebar.php'; ?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-12">
  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include 'template/script.php'; ?>
  </body>
  
  </html>
  ```
- Terapkan arsitektur MVC (Model-View-Controller) secara ketat dalam seluruh pengembangan sistem
- Buat komponen template terpisah (header.php, navbar.php, sidebar.php, script.php) untuk memastikan konsistensi tampilan
- Implementasikan notifikasi windows alert sesuai dengan kebutuhan sistem
- Gunakan struktur folder sesuai dengan konvensi MVC: models/, views/, controllers/, config/, assets/
- Pastikan tidak ada komentar dalam kode produksi
- Gunakan file koneksi database secara konsisten di seluruh sistem
- Implementasikan validasi data di sisi server dan client
- Terapkan keamanan aplikasi (password hashing, CSRF protection, validasi input)
- Gunakan format URL dengan camel case, misalnya: `index.php?controller=kategoriBarang&action=tambah`, `index.php?controller=barang&action=edit`, `index.php?controller=penjualanBarang&action=index`
- Buat sistem routing tambahan atau modifikasi sistem routing di index.php agar mendukung format camel case (melalui file routing_helper.php), karena sistem routing default di index.php menggunakan format kebab-case
- Implementasi tampilan yang dinamis, modern, dan responsif sesuai standar website produksi
- Gunakan komponen UI yang interaktif dan user-friendly
- Terapkan desain yang konsisten di seluruh halaman aplikasi
- Implementasi fitur pencarian dan pagination pada tabel data
- Gunakan ikon dan elemen visual untuk meningkatkan UX
- Terapkan validasi form di sisi client dan server
- Gunakan efek hover dan transisi untuk pengalaman pengguna yang lebih baik
- Hindari penggunaan subheading pada halaman views, cukup gunakan heading utama untuk judul halaman
- Gunakan layout card full untuk seluruh halaman views
- Terapkan desain responsif dengan grid system yang konsisten
- Gunakan ikon yang relevan untuk setiap field input dan aksi
- Gunakan warna yang sesuai untuk status (hijau untuk sukses/ada stok, kuning untuk peringatan, merah untuk error/habis)
- Tampilkan data dalam tabel yang dapat diurutkan dan dicari
- Tampilkan tombol aksi dalam grup tombol yang rapi
- Gunakan format angka yang sesuai untuk harga dan jumlah
- Gunakan tampilan kosong (empty state) saat tidak ada data
- Gunakan badge dan label untuk informasi kategori atau status
- Terapkan validasi form dengan pesan yang informatif
- Gunakan template frontend yang konsisten di seluruh halaman
- Jangan gunakan class text-muted lagi dalam elemen apapun
- Jangan gunakan elemen small sebagai subtext atau petunjuk tambahan

### 9.2 Jangan Dilakukan
- Jangan mengubah struktur file config/koneksi.php
- Jangan mengabaikan struktur MVC yang telah ditentukan
- Jangan menambahkan komentar dalam kode produksi
- Jangan mengabaikan validasi input dan keamanan aplikasi
- Jangan mengakses database langsung tanpa melalui model
- Jangan mengabaikan perbedaan role pengguna (admin, kasir, owner) dalam implementasi fitur
- Jangan mengabaikan konversi format URL dari camel case ke kebab-case saat berinteraksi dengan sistem routing utama