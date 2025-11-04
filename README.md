# 🌾 Agro Tani - Sistem Manajemen Toko Pertanian

Sistem informasi manajemen toko pertanian yang modern dan terintegrasi untuk mengelola pembelian, penjualan, stok barang, dan laporan bisnis.

## 📋 Deskripsi Project

**Agro Tani** adalah aplikasi berbasis web yang dirancang khusus untuk mengelola toko pertanian dengan fitur lengkap mulai dari manajemen inventaris hingga pelaporan keuangan. Sistem ini dikembangkan menggunakan PHP dengan arsitektur MVC (Model-View-Controller) dan database MySQL yang terstruktur dengan baik.

## 🏢 Profil Toko

**Toko Gani Agro Tani**
- 📍 Alamat: Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman, Sumatera Barat
- 🏪 Jenis Usaha: Toko Pertanian (Pupuk, Benih, Pestisida, Alat Pertanian)
- 📊 Sistem: Manajemen Stok, Penjualan, Pembelian, dan Laporan

## ✨ Fitur Utama

### 📱 Dashboard
- Real-time overview bisnis
- Statistik penjualan dan pembelian
- Monitoring stok barang
- Visualisasi data interaktif

### 📦 Manajemen Inventaris
- **Data Barang**: Master data produk dengan kategori
- **Kategori Barang**: Pupuk, Benih, Pestisida, Alat Pertanian
- **Supplier**: Data supplier/pemasok barang
- **Pelanggan**: Database pelanggan toko
- **Stock Management**: Tracking stok real-time dengan status (HABIS/RENDAH/AMAN)

### 💰 Transaksi Bisnis
- **Pembelian Barang**: Sistem pembelian dari supplier dengan nota
- **Penjualan (Kasir)**: Sistem penjualan dengan faktur otomatis
- **Harga Flexibel**: Support harga berbeda per transaksi
- **Multi-payment**: Sistem pembayaran yang fleksibel

### 📊 Sistem Laporan
- **Laporan Penjualan**: Harian, Bulanan, Tahunan dengan filter
- **Laporan Pembelian**: Tracking pembelian barang
- **Laporan Stok**: Monitoring ketersediaan barang
- **Export PDF**: Laporan formal dengan header profesional

### 👥 Manajemen User
- **Role-Based Access Control**:
  - **Admin**: Akses penuh ke semua fitur
  - **Kasir**: Dashboard, Data Pelanggan, Kasir
  - **Owner**: Dashboard, Cetak Laporan
- **Multi-user**: Support beberapa user dengan role berbeda

## 🛠️ Teknologi yang Digunakan

### Backend
- **PHP 8.1+**: Bahasa pemrograman utama
- **MySQL 8.0**: Database management system
- **PDO**: Database abstraction layer
- **MVC Architecture**: Pattern development yang terstruktur

### Frontend
- **Bootstrap 5**: CSS framework untuk responsive design
- **JavaScript**: Interaktivitas client-side
- **Material Design Icons**: Icon library
- **SweetAlert2**: Alert dan notification system

### PDF Generation
- **TCPDF**: Library untuk generating laporan PDF
- **Custom Headers**: Header profesional untuk laporan
- **Multiple Formats**: Landscape & Portrait support

## 📁 Struktur Project

```
agro_tani/
├── controllers/           # Controller logic
│   ├── LaporanController.php
│   ├── BarangController.php
│   ├── PenjualanBarangController.php
│   └── ...
├── models/                # Database logic
│   ├── LaporanModel.php
│   ├── BarangModel.php
│   ├── UserModel.php
│   └── ...
├── views/                 # Template files
│   ├── laporan/
│   ├── barang/
│   ├── penjualan/
│   └── template/
├── helpers/               # Utility functions
├── config/                # Configuration files
├── uploads/               # File uploads
├── vendor/                # Composer dependencies
└── README.md
```

## 🚀 Cara Instalasi

### Prerequisites
- PHP 8.1 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Web server (Apache/Nginx)
- Composer (untuk dependency management)

### Instalasi

1. **Clone repository**
```bash
git clone <repository-url>
cd agro_tani
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup database**
- Buat database baru dengan nama `agro_tani`
- Import file `agro_tani.sql` ke database

4. **Konfigurasi koneksi database**
Edit file `config/koneksi.php`:
```php
$host = 'localhost';
$username = 'your_db_username';
$password = 'your_db_password';
$database = 'agro_tani';
```

5. **Setup virtual host**
Konfigurasi web server untuk pointing ke folder project

6. **Akses aplikasi**
Buka browser dan akses `http://localhost/agro_tani`

### Default Login
- **Admin**: username: `admin`, password: `admin123`
- **Kasir**: username: `kasir`, password: `kasir123`
- **Owner**: username: `owner`, password: `owner123`

## 📱 User Guide

### Role Admin
- **Dashboard**: Monitoring overview bisnis
- **Master Data**: Kelola barang, kategori, supplier, pelanggan
- **Transaksi**: Pembelian dan penjualan barang
- **Laporan**: Cetak semua jenis laporan
- **User Management**: Kelola user dan role

### Role Kasir
- **Dashboard**: Ringkasan penjualan
- **Data Pelanggan**: Kelola database pelanggan
- **Kasir**: Proses transaksi penjualan
- **Laporan**: Cetak laporan penjualan

### Role Owner
- **Dashboard**: Overview bisnis
- **Cetak Laporan**: Akses semua laporan keuangan
- **Monitoring**: Pantau performa bisnis

## 📊 Fitur Laporan

### Jenis Laporan
1. **Laporan Penjualan**
   - Format: Harian, Range Hari, Bulanan, Tahunan
   - Detail: No Faktur, Tanggal, Barang, Jumlah, Harga, Subtotal
   - Export: PDF dengan header formal

2. **Laporan Pembelian**
   - Format: Harian, Range Hari, Bulanan, Tahunan
   - Detail: No Nota, Tanggal, Supplier, Barang, Jumlah, Harga, Subtotal
   - Export: PDF profesional

3. **Laporan Stok Barang**
   - Filter: Semua, Stok Habis, Stok Rendah, Stok Aman
   - Detail: Kode Barang, Kategori, Satuan, Stok, Status
   - Export: PDF Landscape

### Custom Header PDF
- **Formal Header**: Nama toko + alamat + garis pembatas
- **Simple Header**: Judul laporan saja (untuk stok)
- **Signature**: Tanda tangan digital dengan koordinat presisi
- **Customizable**: Nama pimpinan dapat diubah via URL

## 🔒 Keamanan

### Fitur Keamanan
- **Session Management**: Autentikasi user yang aman
- **Role-Based Access**: Akses kontrol berdasarkan role
- **SQL Injection Prevention**: Menggunakan prepared statements
- **Password Encryption**: Hashing password yang aman
- **Input Validation**: Validasi data di client & server side

### Best Practices
- Regular database backup
- Update password secara berkala
- Monitoring user activity
- Secure file upload handling

## 🎨 UI/UX Design

### Features
- **Responsive Design**: Optimal di desktop & mobile
- **Modern Interface**: Menggunakan Bootstrap 5
- **Intuitive Navigation**: Sidebar yang terorganisir
- **Interactive Dashboard**: Real-time data visualization
- **Professional Reports**: Format PDF yang konsisten

### Theme
- **Color Scheme**: Modern business theme
- **Icons**: Material Design Icons
- **Typography**: Font yang mudah dibaca
- **Layout**: Clean dan professional

## 🔄 Workflow Bisnis

### Alur Pembelian
1. Supplier → Create Purchase Order
2. Pembelian → Add Items → Calculate Total
3. Update Stock → Increase inventory
4. Generate Laporan → Track purchases

### Alur Penjualan
1. Pelanggan → Create Sales Order
2. Penjualan → Add Items → Calculate Total
3. Update Stock → Decrease inventory
4. Generate Laporan → Track sales

### Stock Management
- **Real-time Tracking**: Stock update otomatis
- **Status Monitoring**: HABIS/RENDAH/AMAN indicators
- **Alert System**: Notifikasi stok rendah
- **Reporting**: Stock level analysis

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Cek konfigurasi di `config/koneksi.php`
   - Pastikan MySQL service running
   - Verify database credentials

2. **PDF Generation Error**
   - Check TCPDF library installation
   - Verify write permissions di folder temp
   - Ensure font files available

3. **Session Issues**
   - Check PHP session configuration
   - Verify server time settings
   - Clear browser cache

### Support
- 📧 Email: support@agrotani.com
- 📞 Phone: +62 812-3456-7890
- 💬 WhatsApp: +62 812-3456-7890

## 📝 Changelog

### Version 1.0.0 (2025-11-04)
- ✅ Initial release
- ✅ Core CRUD operations
- ✅ User management system
- ✅ PDF reporting system
- ✅ Role-based access control
- ✅ Inventory management
- ✅ Sales & Purchase tracking

## 🤝 Contributing

### How to Contribute
1. Fork repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation
- Maintain backward compatibility

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 👥 Development Team

- **Project Lead**: Gani Agro Tani
- **Backend Developer**: PHP Specialist
- **Frontend Developer**: UI/UX Specialist
- **Database Designer**: MySQL Expert
- **QA Engineer**: Testing & Validation

## 🙏 Acknowledgments

- **Bootstrap Team** - For excellent CSS framework
- **TCPDF Team** - For PDF generation library
- **Material Design** - For icon library
- **PHP Community** - For continuous improvement

## 📞 Contact Information

**Toko Gani Agro Tani**
- 📍 Jl. Raya Tanjung Basung II, Kec. Batang Anai
- 🏢 Kab. Padang Pariaman, Sumatera Barat
- 📱 +62 812-3456-7890
- 📧 info@agrotani.com
- 🌐 www.agrotani.com

---

*© 2025 Toko Gani Agro Tani. All rights reserved.*