<?php
require_once 'models/DashboardModel.php';

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }

        $role = $_SESSION['role'];
        switch($role) {
            case 'admin':
                $this->admin();
                break;
            case 'kasir':
                $this->kasir();
                break;
            case 'owner':
                $this->owner();
                break;
            default:
                header('Location: index.php?controller=auth&action=index');
                exit();
        }
    }

    public function admin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }

        $total_barang = $this->dashboardModel->getTotalBarang();
        $total_stok = $this->dashboardModel->getTotalStok();
        $total_penjualan = $this->dashboardModel->getTotalPenjualan();
        $total_pendapatan = $this->dashboardModel->getTotalPendapatan();
        $barang_habis = $this->dashboardModel->getBarangHabis();
        $barang_stok_rendah = $this->dashboardModel->getBarangStokRendah();
        $penjualan_hari_ini = $this->dashboardModel->getPenjualanHariIni();
        $pendapatan_hari_ini = $this->dashboardModel->getPendapatanHariIni();
        $barang_terlaris = $this->dashboardModel->getBarangTerlaris();
        $penjualan_bulan_ini = $this->dashboardModel->getPenjualanBulanIni();
        $transaksi_terakhir = $this->dashboardModel->getTransaksiTerakhir();

        include 'views/dashboard/admin.php';
    }

    public function kasir() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }

        $total_barang = $this->dashboardModel->getTotalBarang();
        $total_penjualan = $this->dashboardModel->getTotalPenjualan();
        $penjualan_hari_ini = $this->dashboardModel->getPenjualanHariIni();
        $pendapatan_hari_ini = $this->dashboardModel->getPendapatanHariIni();
        $transaksi_terakhir = $this->dashboardModel->getTransaksiTerakhir();

        include 'views/dashboard/kasir.php';
    }

    public function owner() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            header('Location: index.php?controller=auth&action=index');
            exit();
        }

        $total_barang = $this->dashboardModel->getTotalBarang();
        $total_penjualan = $this->dashboardModel->getTotalPenjualan();
        $total_pendapatan = $this->dashboardModel->getTotalPendapatan();
        $barang_habis = $this->dashboardModel->getBarangHabis();
        $penjualan_hari_ini = $this->dashboardModel->getPenjualanHariIni();
        $pendapatan_hari_ini = $this->dashboardModel->getPendapatanHariIni();
        $barang_terlaris = $this->dashboardModel->getBarangTerlaris();
        $penjualan_bulan_ini = $this->dashboardModel->getPenjualanBulanIni();
        $transaksi_terakhir = $this->dashboardModel->getTransaksiTerakhir();

        include 'views/dashboard/owner.php';
    }
}
?>