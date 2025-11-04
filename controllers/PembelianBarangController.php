<?php
require_once 'models/PembelianBarangModel.php';

class PembelianBarangController {
    private $model;

    public function __construct() {
        $this->model = new PembelianBarangModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $pembelian = $this->model->getAll();
        include 'views/pembelian-barang/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
            echo "<script>alert('Akses ditolak!'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $supplier = $this->model->getSupplierAll();
        $barang = $this->model->getBarangAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_supplier = $_POST['id_supplier'];
            $total = $_POST['total'] ?? 0;
            
            // Ambil detail pembelian dari form
            $item_barang = $_POST['item_barang'] ?? [];
            $item_jumlah = $_POST['item_jumlah'] ?? [];
            $item_harga_beli = $_POST['item_harga_beli'] ?? [];
            
            // Generate no nota otomatis
            $no_nota = $this->model->generateNoNotaOtomatis();
            
            // Validasi
            if (empty($id_supplier) || $total <= 0 || empty($item_barang)) {
                echo "<script>alert('Supplier, total, dan item pembelian wajib diisi!'); window.location.href='index.php?controller=pembelianBarang&action=tambah';</script>";
                exit();
            }
            
            // Proses data item
            $items = [];
            $calculated_total = 0;
            
            for ($i = 0; $i < count($item_barang); $i++) {
                $id_barang = $item_barang[$i];
                $jumlah = $item_jumlah[$i];
                $harga_beli = $item_harga_beli[$i];
                $subtotal = $jumlah * $harga_beli;
                
                // Validasi data item
                if (empty($id_barang) || $jumlah <= 0 || $harga_beli <= 0) {
                    echo "<script>alert('Data item pembelian tidak valid!'); window.location.href='index.php?controller=pembelianBarang&action=tambah';</script>";
                    exit();
                }
                
                $items[] = [
                    'id_barang' => $id_barang,
                    'jumlah' => $jumlah,
                    'harga_beli' => $harga_beli,
                    'subtotal' => $subtotal
                ];
                
                $calculated_total += $subtotal;
            }
            
            // Validasi total
            if (abs($total - $calculated_total) > 0.01) { // Toleransi perbedaan kecil
                echo "<script>alert('Total tidak sesuai dengan perhitungan item!'); window.location.href='index.php?controller=pembelianBarang&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah no nota sudah ada
            if ($this->model->existsByNoNota($no_nota)) {
                echo "<script>alert('No nota sudah ada!'); window.location.href='index.php?controller=pembelianBarang&action=tambah';</script>";
                exit();
            }
            
            $id_pembelian = $this->model->create($no_nota, $id_supplier, $total, $_SESSION['user_id'], $items);
            if ($id_pembelian) {
                echo "<script>alert('Pembelian berhasil ditambahkan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan pembelian!'); window.location.href='index.php?controller=pembelianBarang&action=tambah';</script>";
                exit();
            }
            exit();
        }
        
        include 'views/pembelian-barang/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat mengedit pembelian.'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $pembelian = $this->model->getById($id);
        $supplier = $this->model->getSupplierAll();
        $barang = $this->model->getBarangAll();
        
        if (!$pembelian) {
            echo "<script>alert('Pembelian tidak ditemukan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            exit();
        }
        
        // Ambil detail pembelian saat ini
        $detail_pembelian = $this->model->getDetailByPembelianId($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $no_nota = trim($_POST['no_nota']);
            $id_supplier = $_POST['id_supplier'];
            $total = $_POST['total'] ?? 0;
            
            // Ambil detail pembelian dari form
            $item_barang = $_POST['item_barang'] ?? [];
            $item_jumlah = $_POST['item_jumlah'] ?? [];
            $item_harga_beli = $_POST['item_harga_beli'] ?? [];
            
            // Validasi
            if (empty($no_nota) || empty($id_supplier) || $total <= 0 || empty($item_barang)) {
                echo "<script>alert('No nota, supplier, total, dan item pembelian wajib diisi!'); window.location.href='index.php?controller=pembelianBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Proses data item
            $items = [];
            $calculated_total = 0;
            
            for ($i = 0; $i < count($item_barang); $i++) {
                $id_barang = $item_barang[$i];
                $jumlah = $item_jumlah[$i];
                $harga_beli = $item_harga_beli[$i];
                $subtotal = $jumlah * $harga_beli;
                
                // Validasi data item
                if (empty($id_barang) || $jumlah <= 0 || $harga_beli <= 0) {
                    echo "<script>alert('Data item pembelian tidak valid!'); window.location.href='index.php?controller=pembelianBarang&action=edit&id=$id';</script>";
                    exit();
                }
                
                $items[] = [
                    'id_barang' => $id_barang,
                    'jumlah' => $jumlah,
                    'harga_beli' => $harga_beli,
                    'subtotal' => $subtotal
                ];
                
                $calculated_total += $subtotal;
            }
            
            // Validasi total
            if (abs($total - $calculated_total) > 0.01) { // Toleransi perbedaan kecil
                echo "<script>alert('Total tidak sesuai dengan perhitungan item!'); window.location.href='index.php?controller=pembelianBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah no nota sudah ada (selain pembelian saat ini)
            if ($this->model->existsByNoNota($no_nota, $id)) {
                echo "<script>alert('No nota sudah ada!'); window.location.href='index.php?controller=pembelianBarang&action=edit&id=$id';</script>";
                exit();
            }
            
            if ($this->model->update($id, $no_nota, $id_supplier, $total, $items)) {
                echo "<script>alert('Pembelian berhasil diupdate!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate pembelian!'); window.location.href='index.php?controller=pembelianBarang&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/pembelian-barang/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus pembelian.'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $pembelian = $this->model->getById($id);
        
        if (!$pembelian) {
            echo "<script>alert('Pembelian tidak ditemukan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('Pembelian berhasil dihapus!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus pembelian!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
        }
        exit();
    }
    
    public function detail() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $pembelian = $this->model->getById($id);
        $detail_pembelian = $this->model->getDetailByPembelianId($id);
        
        if (!$pembelian) {
            echo "<script>alert('Pembelian tidak ditemukan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
            exit();
        }
        
        include 'views/pembelian-barang/detail.php';
    }
}
?>