<?php
require_once 'config/koneksi.php';

class DashboardModel {
    private $conn;
    private $table_barang = 'barang';
    private $table_penjualan = 'penjualan';
    private $table_pembelian = 'pembelian';
    private $table_pelanggan = 'pelanggan';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getTotalBarang() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_barang;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalStok() {
        $query = "SELECT SUM(stok) as total FROM " . $this->table_barang;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
    }

    public function getTotalPenjualan() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_penjualan;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalPendapatan() {
        $query = "SELECT SUM(total) as total FROM " . $this->table_penjualan;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
    }

    public function getBarangHabis() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_barang . " WHERE stok = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBarangStokRendah($batas = 5) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_barang . " WHERE stok <= :batas AND stok > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batas', $batas, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getPenjualanHariIni() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_penjualan . " WHERE tanggal = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getPendapatanHariIni() {
        $query = "SELECT SUM(total) as total FROM " . $this->table_penjualan . " WHERE tanggal = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
    }

    public function getBarangTerlaris() {
        $query = "SELECT 
                    b.nama_barang,
                    SUM(dp.jumlah) as jumlah_terjual
                  FROM detail_penjualan dp
                  JOIN barang b ON dp.id_barang = b.id_barang
                  JOIN penjualan p ON dp.id_penjualan = p.id_penjualan
                  GROUP BY dp.id_barang
                  ORDER BY jumlah_terjual DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPenjualanBulanIni() {
        $query = "SELECT 
                    DAY(tanggal) as hari,
                    SUM(total) as total
                  FROM " . $this->table_penjualan . "
                  WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())
                  GROUP BY DAY(tanggal)
                  ORDER BY DAY(tanggal)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransaksiTerakhir() {
        $query = "SELECT 
                    p.no_faktur,
                    p.tanggal,
                    p.total,
                    u.nama_lengkap,
                    pl.nama_pelanggan
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN users u ON p.id_user = u.id_user
                  LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                  ORDER BY p.tanggal DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>