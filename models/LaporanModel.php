<?php
require_once 'config/koneksi.php';

class LaporanModel
{
    private $conn;
    private $table_barang = 'barang';
    private $table_kategori_barang = 'kategori_barang';
    private $table_penjualan = 'penjualan';
    private $table_detail_penjualan = 'detail_penjualan';
    private $table_pelanggan = 'pelanggan';
    private $table_pembelian = 'pembelian';
    private $table_detail_pembelian = 'detail_pembelian';
    private $table_supplier = 'supplier';
    private $table_users = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fungsi helper untuk format tanggal Indonesia
    private function formatTanggalIndonesia($tanggal)
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($tanggal);
        $nama_hari = $hari[date('w', $timestamp)];
        $tanggal_num = date('d', $timestamp);
        $nama_bulan = $bulan[date('n', $timestamp) - 1];
        $tahun = date('Y', $timestamp);

        return "$nama_hari, $tanggal_num $nama_bulan $tahun";
    }

    // Laporan Penjualan
    public function getLaporanPenjualanTable($params = [])
    {
        $jenis_filter = $params['jenis_filter'] ?? 'harian';
        $query = "SELECT
                    dp.id_detail,
                    p.no_faktur,
                    p.tanggal,
                    b.kode_barang,
                    b.nama_barang,
                    pl.nama_pelanggan,
                    dp.jumlah,
                    dp.harga_satuan,
                    dp.subtotal,
                    u.nama_lengkap as nama_user
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN " . $this->table_users . " u ON p.id_user = u.id_user";

        $queryParams = [];

        switch ($jenis_filter) {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(p.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;

            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;

            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(p.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;

            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $query .= " ORDER BY p.tanggal DESC, p.no_faktur, b.nama_barang ASC";

        $stmt = $this->conn->prepare($query);

        for ($i = 0; $i < count($queryParams); $i++) {
            $stmt->bindParam($i + 1, $queryParams[$i]);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Laporan Pembelian
    public function getLaporanPembelianTable($params = [])
    {
        $jenis_filter = $params['jenis_filter'] ?? 'harian';
        $query = "SELECT
                    dp.id_detail,
                    pb.no_nota,
                    pb.tanggal,
                    s.nama_supplier,
                    b.kode_barang,
                    b.nama_barang,
                    dp.jumlah,
                    dp.harga_beli,
                    dp.subtotal,
                    u.nama_lengkap as nama_user
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian
                  JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  LEFT JOIN " . $this->table_users . " u ON pb.id_user = u.id_user";

        $queryParams = [];

        switch ($jenis_filter) {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(pb.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;

            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;

            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(pb.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;

            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $query .= " ORDER BY pb.tanggal DESC, pb.no_nota, b.nama_barang ASC";

        $stmt = $this->conn->prepare($query);

        for ($i = 0; $i < count($queryParams); $i++) {
            $stmt->bindParam($i + 1, $queryParams[$i]);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Laporan Stok Barang
    public function getLaporanStokBarangTable($params = [])
    {
        $jenis_stok = $params['jenis_stok'] ?? 'semua';
        $batas_stok = $params['batas_stok'] ?? 5;

        $query = "SELECT
                    b.id_barang,
                    b.kode_barang,
                    b.nama_barang,
                    b.satuan,
                    b.stok,
                    kb.nama_kategori,
                    CASE
                        WHEN b.stok = 0 THEN 'HABIS'
                        WHEN b.stok <= " . (int)$batas_stok . " THEN 'RENDAH'
                        ELSE 'AMAN'
                    END as status_barang
                  FROM " . $this->table_barang . " b
                  LEFT JOIN " . $this->table_kategori_barang . " kb ON b.id_kategori = kb.id_kategori";

        switch ($jenis_stok) {
            case 'habis':
                $query .= " WHERE b.stok = 0";
                break;
            case 'rendah':
                $query .= " WHERE b.stok > 0 AND b.stok <= ?";
                break;
            case 'aman':
                $query .= " WHERE b.stok > ?";
                break;
            // 'semua' tidak perlu WHERE clause
        }

        $query .= " ORDER BY kb.nama_kategori ASC, b.nama_barang ASC";

        $stmt = $this->conn->prepare($query);

        if ($jenis_stok !== 'semua') {
            if ($jenis_stok === 'rendah' || $jenis_stok === 'aman') {
                $stmt->bindParam(1, $batas_stok);
            }
            // 'habis' tidak perlu parameter
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk mendapatkan total keseluruhan penjualan
    public function getTotalPenjualan($params = [])
    {
        $jenis_filter = $params['jenis_filter'] ?? 'harian';
        $query = "SELECT SUM(dp.subtotal) as total
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan";

        $queryParams = [];

        switch ($jenis_filter) {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(p.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;

            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;

            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(p.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;

            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $stmt = $this->conn->prepare($query);

        for ($i = 0; $i < count($queryParams); $i++) {
            $stmt->bindParam($i + 1, $queryParams[$i]);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    }

    // Method untuk mendapatkan total keseluruhan pembelian
    public function getTotalPembelian($params = [])
    {
        $jenis_filter = $params['jenis_filter'] ?? 'harian';
        $query = "SELECT SUM(dp.subtotal) as total
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian";

        $queryParams = [];

        switch ($jenis_filter) {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(pb.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;

            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;

            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(pb.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;

            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $stmt = $this->conn->prepare($query);

        for ($i = 0; $i < count($queryParams); $i++) {
            $stmt->bindParam($i + 1, $queryParams[$i]);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total'] : 0;
    }

    // Method lama untuk kompatibilitas
    public function getLaporanPenjualanHarian($tanggal) {
        $query = "SELECT p.*, pl.nama_pelanggan, u.nama_lengkap as nama_user
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN users u ON p.id_user = u.id_user
                  WHERE DATE(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanPenjualanBulanan($bulan, $tahun) {
        $query = "SELECT p.*, pl.nama_pelanggan, u.nama_lengkap as nama_user
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN users u ON p.id_user = u.id_user
                  WHERE MONTH(p.tanggal) = ? AND YEAR(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bulan);
        $stmt->bindParam(2, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanPenjualanTahunan($tahun) {
        $query = "SELECT p.*, pl.nama_pelanggan, u.nama_lengkap as nama_user
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN users u ON p.id_user = u.id_user
                  WHERE YEAR(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPenjualanHarian($tanggal) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, p.no_faktur, pl.nama_pelanggan
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  WHERE DATE(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPenjualanBulanan($bulan, $tahun) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, p.no_faktur, pl.nama_pelanggan
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  WHERE MONTH(p.tanggal) = ? AND YEAR(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bulan);
        $stmt->bindParam(2, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPenjualanTahunan($tahun) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, p.no_faktur, pl.nama_pelanggan
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  WHERE YEAR(p.tanggal) = ?
                  ORDER BY p.tanggal DESC, p.no_faktur, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanPembelianHarian($tanggal) {
        $query = "SELECT pb.*, s.nama_supplier, u.nama_lengkap as nama_user
                  FROM " . $this->table_pembelian . " pb
                  LEFT JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  LEFT JOIN users u ON pb.id_user = u.id_user
                  WHERE DATE(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanPembelianBulanan($bulan, $tahun) {
        $query = "SELECT pb.*, s.nama_supplier, u.nama_lengkap as nama_user
                  FROM " . $this->table_pembelian . " pb
                  LEFT JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  LEFT JOIN users u ON pb.id_user = u.id_user
                  WHERE MONTH(pb.tanggal) = ? AND YEAR(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bulan);
        $stmt->bindParam(2, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanPembelianTahunan($tahun) {
        $query = "SELECT pb.*, s.nama_supplier, u.nama_lengkap as nama_user
                  FROM " . $this->table_pembelian . " pb
                  LEFT JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  LEFT JOIN users u ON pb.id_user = u.id_user
                  WHERE YEAR(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPembelianHarian($tanggal) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, pb.no_nota, s.nama_supplier
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian
                  JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  WHERE DATE(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tanggal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPembelianBulanan($bulan, $tahun) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, pb.no_nota, s.nama_supplier
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian
                  JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  WHERE MONTH(pb.tanggal) = ? AND YEAR(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bulan);
        $stmt->bindParam(2, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailPembelianTahunan($tahun) {
        $query = "SELECT dp.*, b.nama_barang, b.kode_barang, b.satuan, pb.no_nota, s.nama_supplier
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian
                  JOIN " . $this->table_supplier . " s ON pb.id_supplier = s.id_supplier
                  WHERE YEAR(pb.tanggal) = ?
                  ORDER BY pb.tanggal DESC, pb.no_nota, b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tahun);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanStokBarang() {
        $query = "SELECT b.*, kb.nama_kategori
                  FROM " . $this->table_barang . " b
                  LEFT JOIN " . $this->table_kategori_barang . " kb ON b.id_kategori = kb.id_kategori
                  ORDER BY b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLaporanStokBarangKurang($batas = 5) {
        $query = "SELECT b.*, kb.nama_kategori
                  FROM " . $this->table_barang . " b
                  LEFT JOIN " . $this->table_kategori_barang . " kb ON b.id_kategori = kb.id_kategori
                  WHERE b.stok <= ?
                  ORDER BY b.stok ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $batas);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fungsi tambahan untuk statistik
    public function getStatistikPenjualan($params = [])
    {
        $query = "SELECT
                    COUNT(DISTINCT p.id_penjualan) as total_transaksi,
                    COUNT(dp.id_detail) as total_item,
                    SUM(dp.subtotal) as total_nilai
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan";

        $queryParams = [];

        switch ($params['jenis_filter'] ?? 'harian') {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(p.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;
            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;
            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(p.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;
            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(p.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatistikPembelian($params = [])
    {
        $query = "SELECT
                    COUNT(DISTINCT pb.id_pembelian) as total_transaksi,
                    COUNT(dp.id_detail) as total_item,
                    SUM(dp.subtotal) as total_nilai
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian";

        $queryParams = [];

        switch ($params['jenis_filter'] ?? 'harian') {
            case 'harian':
                if (!empty($params['tanggal_mulai'])) {
                    $query .= " WHERE DATE(pb.tanggal) = ?";
                    $queryParams[] = $params['tanggal_mulai'];
                }
                break;
            case 'range-hari':
                if (!empty($params['tanggal_mulai']) && !empty($params['tanggal_akhir'])) {
                    $query .= " WHERE DATE(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tanggal_mulai'];
                    $queryParams[] = $params['tanggal_akhir'];
                }
                break;
            case 'range-bulan':
                if (!empty($params['bulan_mulai']) && !empty($params['bulan_akhir'])) {
                    $query .= " WHERE (DATE_FORMAT(pb.tanggal, '%Y-%m') BETWEEN ? AND ?)";
                    $queryParams[] = $params['bulan_mulai'];
                    $queryParams[] = $params['bulan_akhir'];
                }
                break;
            case 'range-tahun':
                if (!empty($params['tahun_mulai']) && !empty($params['tahun_akhir'])) {
                    $query .= " WHERE YEAR(pb.tanggal) BETWEEN ? AND ?";
                    $queryParams[] = $params['tahun_mulai'];
                    $queryParams[] = $params['tahun_akhir'];
                }
                break;
        }

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatistikStok()
    {
        $query = "SELECT
                    COUNT(b.id_barang) as total_barang,
                    SUM(CASE WHEN b.stok = 0 THEN 1 ELSE 0 END) as barang_habis,
                    SUM(CASE WHEN b.stok <= 5 THEN 1 ELSE 0 END) as barang_rendah,
                    SUM(CASE WHEN b.stok > 5 THEN 1 ELSE 0 END) as barang_aman,
                    SUM(b.stok) as total_stok
                  FROM " . $this->table_barang . " b
                  LEFT JOIN " . $this->table_kategori_barang . " kb ON b.id_kategori = kb.id_kategori";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method untuk mendapatkan ringkasan laporan
    public function getRingkasanLaporan()
    {
        $query = "SELECT
                    'penjualan' as jenis,
                    COUNT(DISTINCT p.id_penjualan) as total_transaksi,
                    SUM(dp.subtotal) as total_nilai,
                    DATE(MAX(p.tanggal)) as tanggal_terakhir
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_penjualan . " p ON dp.id_penjualan = p.id_penjualan
                  GROUP BY 'penjualan'
                  UNION ALL
                  SELECT
                    'pembelian' as jenis,
                    COUNT(DISTINCT pb.id_pembelian) as total_transaksi,
                    SUM(dp.subtotal) as total_nilai,
                    DATE(MAX(pb.tanggal)) as tanggal_terakhir
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_pembelian . " pb ON dp.id_pembelian = pb.id_pembelian
                  GROUP BY 'pembelian'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>