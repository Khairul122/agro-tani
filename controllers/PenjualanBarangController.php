<?php
require_once 'models/PenjualanBarangModel.php';

class PenjualanBarangController {
    private $model;

    public function __construct() {
        $this->model = new PenjualanBarangModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $penjualan = $this->model->getAll();
        include 'views/penjualan/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $pelanggan = $this->model->getPelangganAll();
        $barang = $this->model->getBarangAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pelanggan = $_POST['id_pelanggan'];
            $total = $_POST['total'] ?? 0;
            
            // Ambil detail penjualan dari form
            $item_barang = $_POST['item_barang'] ?? [];
            $item_jumlah = $_POST['item_jumlah'] ?? [];
            $item_harga_satuan = $_POST['item_harga_satuan'] ?? [];
            
            // Generate no faktur otomatis
            $no_faktur = $this->model->generateNoFakturOtomatis();
            
            // Validasi
            if (empty($id_pelanggan) || $total <= 0 || empty($item_barang)) {
                echo "<script>alert('Pelanggan, total, dan item penjualan wajib diisi!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                exit();
            }
            
            // Proses data item
            $items = [];
            $calculated_total = 0;
            
            for ($i = 0; $i < count($item_barang); $i++) {
                $id_barang = $item_barang[$i];
                $jumlah = $item_jumlah[$i];
                $harga_satuan = $item_harga_satuan[$i];
                $subtotal = $jumlah * $harga_satuan;
                
                // Validasi data item
                if (empty($id_barang) || $jumlah <= 0 || $harga_satuan <= 0) {
                    echo "<script>alert('Data item penjualan tidak valid!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                    exit();
                }
                
                // Cek stok barang
                $barang_data = array_filter($barang, function($b) use ($id_barang) {
                    return $b['id_barang'] == $id_barang;
                });
                
                if (empty($barang_data)) {
                    echo "<script>alert('Barang tidak ditemukan!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                    exit();
                }
                
                $barang_obj = reset($barang_data);
                if ($jumlah > $barang_obj['stok']) {
                    echo "<script>alert('Jumlah melebihi stok barang!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                    exit();
                }
                
                $items[] = [
                    'id_barang' => $id_barang,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga_satuan,
                    'subtotal' => $subtotal
                ];
                
                $calculated_total += $subtotal;
            }
            
            // Validasi total
            if (abs($total - $calculated_total) > 0.01) { // Toleransi perbedaan kecil
                echo "<script>alert('Total tidak sesuai dengan perhitungan item!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah no faktur sudah ada
            if ($this->model->existsByNoFaktur($no_faktur)) {
                echo "<script>alert('No faktur sudah ada!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                exit();
            }
            
            $id_penjualan = $this->model->create($no_faktur, $id_pelanggan, $total, $_SESSION['user_id'], $items);
            if ($id_penjualan) {
                echo "<script>alert('Penjualan berhasil ditambahkan!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan penjualan!'); window.location.href='index.php?controller=penjualanBarang&action=tambah';</script>";
                exit();
            }
            exit();
        }
        
        include 'views/penjualan/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat mengedit penjualan.'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $penjualan = $this->model->getById($id);
        $pelanggan = $this->model->getPelangganAll();
        $barang = $this->model->getBarangAll();
        
        if (!$penjualan) {
            echo "<script>alert('Penjualan tidak ditemukan!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            exit();
        }
        
        // Ambil detail penjualan saat ini
        $detail_penjualan = $this->model->getDetailByPenjualanId($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $no_faktur = trim($_POST['no_faktur']);
            $id_pelanggan = $_POST['id_pelanggan'];
            $total = $_POST['total'] ?? 0;
            
            // Ambil detail penjualan dari form
            $item_barang = $_POST['item_barang'] ?? [];
            $item_jumlah = $_POST['item_jumlah'] ?? [];
            $item_harga_satuan = $_POST['item_harga_satuan'] ?? [];
            
            // Validasi
            if (empty($no_faktur) || empty($id_pelanggan) || $total <= 0 || empty($item_barang)) {
                echo "<script>alert('No faktur, pelanggan, total, dan item penjualan wajib diisi!'); window.location.href='index.php?controller=penjualanBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Proses data item
            $items = [];
            $calculated_total = 0;
            
            for ($i = 0; $i < count($item_barang); $i++) {
                $id_barang = $item_barang[$i];
                $jumlah = $item_jumlah[$i];
                $harga_satuan = $item_harga_satuan[$i];
                $subtotal = $jumlah * $harga_satuan;
                
                // Validasi data item
                if (empty($id_barang) || $jumlah <= 0 || $harga_satuan <= 0) {
                    echo "<script>alert('Data item penjualan tidak valid!'); window.location.href='index.php?controller=penjualanBarang&action=edit&id=$id';</script>";
                    exit();
                }
                
                $items[] = [
                    'id_barang' => $id_barang,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga_satuan,
                    'subtotal' => $subtotal
                ];
                
                $calculated_total += $subtotal;
            }
            
            // Validasi total
            if (abs($total - $calculated_total) > 0.01) { // Toleransi perbedaan kecil
                echo "<script>alert('Total tidak sesuai dengan perhitungan item!'); window.location.href='index.php?controller=penjualanBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah no faktur sudah ada (selain penjualan saat ini)
            if ($this->model->existsByNoFaktur($no_faktur, $id)) {
                echo "<script>alert('No faktur sudah ada!'); window.location.href='index.php?controller=penjualanBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $no_faktur, $id_pelanggan, $total, $items)) {
                echo "<script>alert('Penjualan berhasil diupdate!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate penjualan!'); window.location.href='index.php?controller=penjualanBarang&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/penjualan/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus penjualan.'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $penjualan = $this->model->getById($id);
        
        if (!$penjualan) {
            echo "<script>alert('Penjualan tidak ditemukan!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Penjualan berhasil dihapus!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus penjualan!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
        }
        exit();
    }
    
    public function detail() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $penjualan = $this->model->getById($id);
        $detail_penjualan = $this->model->getDetailByPenjualanId($id);
        
        if (!$penjualan) {
            echo "<script>alert('Penjualan tidak ditemukan!'); window.location.href='index.php?controller=penjualanBarang&action=index';</script>";
            exit();
        }
        
        include 'views/penjualan/detail.php';
    }
}
?>