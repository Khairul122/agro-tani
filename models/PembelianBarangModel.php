<?php
require_once 'config/koneksi.php';

class PembelianBarangModel {
    private $conn;
    private $table_pembelian = 'pembelian';
    private $table_detail_pembelian = 'detail_pembelian';
    private $table_barang = 'barang';
    private $table_supplier = 'supplier';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT p.*, s.nama_supplier, u.nama_lengkap as nama_user 
                  FROM " . $this->table_pembelian . " p
                  LEFT JOIN " . $this->table_supplier . " s ON p.id_supplier = s.id_supplier
                  LEFT JOIN users u ON p.id_user = u.id_user
                  ORDER BY p.tanggal DESC, p.no_nota DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.*, s.nama_supplier, u.nama_lengkap as nama_user 
                  FROM " . $this->table_pembelian . " p
                  LEFT JOIN " . $this->table_supplier . " s ON p.id_supplier = s.id_supplier
                  LEFT JOIN users u ON p.id_user = u.id_user
                  WHERE p.id_pembelian = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($no_nota, $id_supplier, $total, $id_user, $items = []) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Insert ke tabel pembelian
            $query_pembelian = "INSERT INTO " . $this->table_pembelian . " SET 
                                no_nota = :no_nota,
                                id_supplier = :id_supplier,
                                total = :total,
                                id_user = :id_user,
                                tanggal = CURDATE()";
            $stmt_pembelian = $this->conn->prepare($query_pembelian);
            $stmt_pembelian->bindParam(':no_nota', $no_nota);
            $stmt_pembelian->bindParam(':id_supplier', $id_supplier);
            $stmt_pembelian->bindParam(':total', $total);
            $stmt_pembelian->bindParam(':id_user', $id_user);
            
            if (!$stmt_pembelian->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            $id_pembelian = $this->conn->lastInsertId();
            
            // Jika ada item, proses detail pembelian
            if (!empty($items)) {
                foreach ($items as $item) {
                    $query_detail = "INSERT INTO " . $this->table_detail_pembelian . " SET 
                                     id_pembelian = :id_pembelian,
                                     id_barang = :id_barang,
                                     jumlah = :jumlah,
                                     harga_beli = :harga_beli,
                                     subtotal = :subtotal";
                    $stmt_detail = $this->conn->prepare($query_detail);
                    $stmt_detail->bindParam(':id_pembelian', $id_pembelian);
                    $stmt_detail->bindParam(':id_barang', $item['id_barang']);
                    $stmt_detail->bindParam(':jumlah', $item['jumlah']);
                    $stmt_detail->bindParam(':harga_beli', $item['harga_beli']);
                    $stmt_detail->bindParam(':subtotal', $item['subtotal']);
                    
                    if (!$stmt_detail->execute()) {
                        $this->conn->rollback();
                        return false;
                    }
                    
                    // Update stok barang
                    $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok + :jumlah WHERE id_barang = :id_barang";
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
            
            return $id_pembelian;
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->conn->rollback();
            return false;
        }
    }

    public function addDetail($id_pembelian, $id_barang, $jumlah, $harga_beli, $subtotal) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Insert ke detail_pembelian
            $query_detail = "INSERT INTO " . $this->table_detail_pembelian . " SET 
                             id_pembelian = :id_pembelian,
                             id_barang = :id_barang,
                             jumlah = :jumlah,
                             harga_beli = :harga_beli,
                             subtotal = :subtotal";
            $stmt_detail = $this->conn->prepare($query_detail);
            $stmt_detail->bindParam(':id_pembelian', $id_pembelian);
            $stmt_detail->bindParam(':id_barang', $id_barang);
            $stmt_detail->bindParam(':jumlah', $jumlah);
            $stmt_detail->bindParam(':harga_beli', $harga_beli);
            $stmt_detail->bindParam(':subtotal', $subtotal);
            
            if (!$stmt_detail->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Update stok barang
            $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok + :jumlah WHERE id_barang = :id_barang";
            $stmt_update_stok = $this->conn->prepare($query_update_stok);
            $stmt_update_stok->bindParam(':jumlah', $jumlah);
            $stmt_update_stok->bindParam(':id_barang', $id_barang);
            
            if (!$stmt_update_stok->execute()) {
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
    
    public function update($id, $no_nota, $id_supplier, $total, $items = []) {
        // Mulai transaksi
        $this->conn->beginTransaction();
        
        try {
            // Update total pembelian
            $query_pembelian = "UPDATE " . $this->table_pembelian . " SET 
                                no_nota = :no_nota,
                                id_supplier = :id_supplier,
                                total = :total
                                WHERE id_pembelian = :id";
            $stmt_pembelian = $this->conn->prepare($query_pembelian);
            $stmt_pembelian->bindParam(':no_nota', $no_nota);
            $stmt_pembelian->bindParam(':id_supplier', $id_supplier);
            $stmt_pembelian->bindParam(':total', $total);
            $stmt_pembelian->bindParam(':id', $id);
            
            if (!$stmt_pembelian->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Hapus detail pembelian lama
            $query_hapus_detail = "DELETE FROM " . $this->table_detail_pembelian . " WHERE id_pembelian = :id_pembelian";
            $stmt_hapus_detail = $this->conn->prepare($query_hapus_detail);
            $stmt_hapus_detail->bindParam(':id_pembelian', $id);
            
            if (!$stmt_hapus_detail->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Reset stok barang berdasarkan detail lama
            $detail_lama = $this->getDetailByPembelianId($id);
            foreach ($detail_lama as $item_lama) {
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok - :jumlah WHERE id_barang = :id_barang";
                $stmt_update_stok = $this->conn->prepare($query_update_stok);
                $stmt_update_stok->bindParam(':jumlah', $item_lama['jumlah']);
                $stmt_update_stok->bindParam(':id_barang', $item_lama['id_barang']);
                
                if (!$stmt_update_stok->execute()) {
                    $this->conn->rollback();
                    return false;
                }
            }
            
            // Tambahkan detail pembelian baru
            $barang = $this->getBarangAll();
            $barang_map = [];
            foreach ($barang as $b) {
                $barang_map[$b['id_barang']] = $b;
            }
            
            foreach ($items as $item) {
                $query_detail = "INSERT INTO " . $this->table_detail_pembelian . " SET 
                                 id_pembelian = :id_pembelian,
                                 id_barang = :id_barang,
                                 jumlah = :jumlah,
                                 harga_beli = :harga_beli,
                                 subtotal = :subtotal";
                $stmt_detail = $this->conn->prepare($query_detail);
                $stmt_detail->bindParam(':id_pembelian', $id);
                $stmt_detail->bindParam(':id_barang', $item['id_barang']);
                $stmt_detail->bindParam(':jumlah', $item['jumlah']);
                $stmt_detail->bindParam(':harga_beli', $item['harga_beli']);
                $stmt_detail->bindParam(':subtotal', $item['subtotal']);
                
                if (!$stmt_detail->execute()) {
                    $this->conn->rollback();
                    return false;
                }
                
                // Update stok barang
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok + :jumlah WHERE id_barang = :id_barang";
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
            // Dapatkan detail pembelian sebelum dihapus
            $detail = $this->getDetailByPembelianId($id);
            
            // Hapus detail pembelian
            $query_hapus_detail = "DELETE FROM " . $this->table_detail_pembelian . " WHERE id_pembelian = :id_pembelian";
            $stmt_hapus_detail = $this->conn->prepare($query_hapus_detail);
            $stmt_hapus_detail->bindParam(':id_pembelian', $id);
            
            if (!$stmt_hapus_detail->execute()) {
                $this->conn->rollback();
                return false;
            }
            
            // Kurangi stok barang sesuai jumlah yang dihapus
            foreach ($detail as $item) {
                $query_update_stok = "UPDATE " . $this->table_barang . " SET stok = stok - :jumlah WHERE id_barang = :id_barang";
                $stmt_update_stok = $this->conn->prepare($query_update_stok);
                $stmt_update_stok->bindParam(':jumlah', $item['jumlah']);
                $stmt_update_stok->bindParam(':id_barang', $item['id_barang']);
                
                if (!$stmt_update_stok->execute()) {
                    $this->conn->rollback();
                    return false;
                }
            }
            
            // Hapus pembelian
            $query_hapus_pembelian = "DELETE FROM " . $this->table_pembelian . " WHERE id_pembelian = :id";
            $stmt_hapus_pembelian = $this->conn->prepare($query_hapus_pembelian);
            $stmt_hapus_pembelian->bindParam(':id', $id);
            
            if (!$stmt_hapus_pembelian->execute()) {
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

    public function existsByNoNota($no_nota, $id_pembelian = null) {
        if ($id_pembelian) {
            $query = "SELECT COUNT(*) FROM " . $this->table_pembelian . " WHERE no_nota = :no_nota AND id_pembelian != :id_pembelian";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table_pembelian . " WHERE no_nota = :no_nota";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':no_nota', $no_nota);
        
        if ($id_pembelian) {
            $stmt->bindParam(':id_pembelian', $id_pembelian);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getBarangAll() {
        $query = "SELECT * FROM " . $this->table_barang . " ORDER BY nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupplierAll() {
        $query = "SELECT * FROM " . $this->table_supplier . " ORDER BY nama_supplier ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDetailByPembelianId($id_pembelian) {
        $query = "SELECT dp.id_detail, dp.id_pembelian, dp.id_barang, dp.jumlah, dp.harga_beli, dp.subtotal,
                         b.nama_barang, b.kode_barang, b.satuan
                  FROM " . $this->table_detail_pembelian . " dp
                  JOIN " . $this->table_barang . " b ON dp.id_barang = b.id_barang
                  WHERE dp.id_pembelian = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_pembelian);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function generateNoNotaOtomatis() {
        // Ambil no nota terakhir
        $query = "SELECT no_nota FROM " . $this->table_pembelian . " WHERE no_nota LIKE 'PM%' ORDER BY no_nota DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $last_no_nota = $stmt->fetchColumn();
        
        if ($last_no_nota) {
            // Ekstrak nomor dari no nota terakhir
            $last_number = (int) substr($last_no_nota, 2); // Ambil angka setelah "PM"
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        // Format no nota baru
        $no_nota = "PM" . sprintf('%05d', $next_number);
        return $no_nota;
    }
}
?>