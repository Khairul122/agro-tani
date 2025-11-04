<?php
require_once 'config/koneksi.php';

class PenjualanBarangModel {
    private $conn;
    private $table_penjualan = 'penjualan';
    private $table_detail_penjualan = 'detail_penjualan';
    private $table_barang = 'barang';
    private $table_pelanggan = 'pelanggan';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT p.*, pl.nama_pelanggan, u.nama_lengkap as nama_user 
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN users u ON p.id_user = u.id_user
                  ORDER BY p.tanggal DESC, p.no_faktur DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.*, pl.nama_pelanggan, u.nama_lengkap as nama_user 
                  FROM " . $this->table_penjualan . " p
                  LEFT JOIN " . $this->table_pelanggan . " pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN users u ON p.id_user = u.id_user
                  WHERE p.id_penjualan = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetailByPenjualanId($id_penjualan) {
        $query = "SELECT dp.id_detail, dp.id_penjualan, dp.id_barang, dp.jumlah, dp.harga_satuan, dp.subtotal,
                         b.nama_barang, b.kode_barang, b.satuan
                  FROM " . $this->table_detail_penjualan . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  WHERE dp.id_penjualan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_penjualan);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($no_faktur, $id_pelanggan, $total, $id_user, $items = []) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Insert ke tabel penjualan
            $query_penjualan = "INSERT INTO " . $this->table_penjualan . " SET 
                                no_faktur = :no_faktur,
                                id_pelanggan = :id_pelanggan,
                                total = :total,
                                id_user = :id_user,
                                tanggal = CURDATE()";
            $stmt_penjualan = $this->conn->prepare($query_penjualan);
            $stmt_penjualan->bindParam(':no_faktur', $no_faktur);
            $stmt_penjualan->bindParam(':id_pelanggan', $id_pelanggan);
            $stmt_penjualan->bindParam(':total', $total);
            $stmt_penjualan->bindParam(':id_user', $id_user);
            
            if (!$stmt_penjualan->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            $id_penjualan = $this->conn->lastInsertId();
            
            // Jika ada item, proses detail penjualan
            if (!empty($items)) {
                foreach ($items as $item) {
                    $query_detail = "INSERT INTO " . $this->table_detail_penjualan . " SET 
                                     id_penjualan = :id_penjualan,
                                     id_barang = :id_barang,
                                     jumlah = :jumlah,
                                     harga_satuan = :harga_satuan,
                                     subtotal = :subtotal";
                    $stmt_detail = $this->conn->prepare($query_detail);
                    $stmt_detail->bindParam(':id_penjualan', $id_penjualan);
                    $stmt_detail->bindParam(':id_barang', $item['id_barang']);
                    $stmt_detail->bindParam(':jumlah', $item['jumlah']);
                    $stmt_detail->bindParam(':harga_satuan', $item['harga_satuan']);
                    $stmt_detail->bindParam(':subtotal', $item['subtotal']);
                    
                    if (!$stmt_detail->execute()) {
                        $this->conn->rollback();
                        return false;
                    }
                    
                    // Kurangi stok barang
                    $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok - :jumlah WHERE id_barang = :id_barang";
                    $stmt_update_stok = $this->conn->prepare($query_update_stok);
                    $stmt_update_stok->bindParam(':jumlah', $item['jumlah']);
                    $stmt_update_stok->bindParam(':id_barang', $item['id_barang']);
                    
                    if (!$stmt_update_stok->execute()) {
                        $this->conn->rollback();
                        return false;
                    }
                }
            }
            
            // Commit transaksi
            $this->conn->commit();
            
            return $id_penjualan;
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->conn->rollback();
            return false;
        }
    }

    public function update($id, $no_faktur, $id_pelanggan, $total, $items = []) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Update total penjualan
            $query_penjualan = "UPDATE " . $this->table_penjualan . " SET 
                                no_faktur = :no_faktur,
                                id_pelanggan = :id_pelanggan,
                                total = :total
                                WHERE id_penjualan = :id";
            $stmt_penjualan = $this->conn->prepare($query_penjualan);
            $stmt_penjualan->bindParam(':no_faktur', $no_faktur);
            $stmt_penjualan->bindParam(':id_pelanggan', $id_pelanggan);
            $stmt_penjualan->bindParam(':total', $total);
            $stmt_penjualan->bindParam(':id', $id);
            
            if (!$stmt_penjualan->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Hapus detail penjualan lama
            $query_hapus_detail = "DELETE FROM " . $this->table_detail_penjualan . " WHERE id_penjualan = :id_penjualan";
            $stmt_hapus_detail = $this->conn->prepare($query_hapus_detail);
            $stmt_hapus_detail->bindParam(':id_penjualan', $id);
            
            if (!$stmt_hapus_detail->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Reset stok barang berdasarkan detail lama
            $detail_lama = $this->getDetailByPenjualanId($id);
            foreach ($detail_lama as $item_lama) {
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok + :jumlah WHERE id_barang = :id_barang";
                $stmt_update_stok = $this->conn->prepare($query_update_stok);
                $stmt_update_stok->bindParam(':jumlah', $item_lama['jumlah']);
                $stmt_update_stok->bindParam(':id_barang', $item_lama['id_barang']);
                
                if (!$stmt_update_stok->execute()) {
                    $this->conn->rollback();
                    return false;
                }
            }
            
            // Tambahkan detail penjualan baru
            foreach ($items as $item) {
                $query_detail = "INSERT INTO " . $this->table_detail_penjualan . " SET 
                                 id_penjualan = :id_penjualan,
                                 id_barang = :id_barang,
                                 jumlah = :jumlah,
                                 harga_satuan = :harga_satuan,
                                 subtotal = :subtotal";
                $stmt_detail = $this->conn->prepare($query_detail);
                $stmt_detail->bindParam(':id_penjualan', $id);
                $stmt_detail->bindParam(':id_barang', $item['id_barang']);
                $stmt_detail->bindParam(':jumlah', $item['jumlah']);
                $stmt_detail->bindParam(':harga_satuan', $item['harga_satuan']);
                $stmt_detail->bindParam(':subtotal', $item['subtotal']);
                
                if (!$stmt_detail->execute()) {
                    $this->conn->rollback();
                    return false;
                }
                
                // Kurangi stok barang
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok - :jumlah WHERE id_barang = :id_barang";
                $stmt_update_stok = $this->conn->prepare($query_update_stok);
                $stmt_update_stok->bindParam(':jumlah', $item['jumlah']);
                $stmt_update_stok->bindParam(':id_barang', $item['id_barang']);
                
                if (!$stmt_update_stok->execute()) {
                    $this->conn->rollback();
                    return false;
                }
            }
            
            // Commit transaksi
            $this->conn->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->conn->rollback();
            return false;
        }
    }

    public function delete($id) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Dapatkan detail penjualan sebelum dihapus
            $detail = $this->getDetailByPenjualanId($id);
            
            // Hapus detail penjualan
            $query_hapus_detail = "DELETE FROM " . $this->table_detail_penjualan . " WHERE id_penjualan = :id_penjualan";
            $stmt_hapus_detail = $this->conn->prepare($query_hapus_detail);
            $stmt_hapus_detail->bindParam(':id_penjualan', $id);
            
            if (!$stmt_hapus_detail->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Kembalikan stok barang sesuai jumlah yang dihapus
            foreach ($detail as $item) {
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok + :jumlah WHERE id_barang = :id_barang";
                $stmt_update_stok = $this->conn->prepare($query_update_stok);
                $stmt_update_stok->bindParam(':jumlah', $item['jumlah']);
                $stmt_update_stok->bindParam(':id_barang', $item['id_barang']);
                
                if (!$stmt_update_stok->execute()) {
                    $this->conn->rollback();
                    return false;
                }
            }
            
            // Hapus penjualan
            $query_hapus_penjualan = "DELETE FROM " . $this->table_penjualan . " WHERE id_penjualan = :id";
            $stmt_hapus_penjualan = $this->conn->prepare($query_hapus_penjualan);
            $stmt_hapus_penjualan->bindParam(':id', $id);
            
            if (!$stmt_hapus_penjualan->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Commit transaksi
            $this->conn->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->conn->rollback();
            return false;
        }
    }

    public function existsByNoFaktur($no_faktur, $id_penjualan = null) {
        if ($id_penjualan) {
            $query = "SELECT COUNT(*) FROM " . $this->table_penjualan . " WHERE no_faktur = :no_faktur AND id_penjualan != :id_penjualan";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table_penjualan . " WHERE no_faktur = :no_faktur";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':no_faktur', $no_faktur);
        
        if ($id_penjualan) {
            $stmt->bindParam(':id_penjualan', $id_penjualan);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getBarangAll() {
        $query = "SELECT * FROM " . $this->table_barang . " WHERE stok > 0 ORDER BY nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPelangganAll() {
        $query = "SELECT * FROM " . $this->table_pelanggan . " ORDER BY nama_pelanggan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function generateNoFakturOtomatis() {
        // Ambil no faktur terakhir
        $query = "SELECT no_faktur FROM " . $this->table_penjualan . " WHERE no_faktur LIKE 'PJ%' ORDER BY no_faktur DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $last_no_faktur = $stmt->fetchColumn();
        
        if ($last_no_faktur) {
            // Ekstrak nomor dari no faktur terakhir
            $last_number = (int) substr($last_no_faktur, 2); // Ambil angka setelah "PJ"
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        // Format no faktur baru
        $no_faktur = "PJ" . sprintf('%05d', $next_number);
        return $no_faktur;
    }
}
?>