<?php
require_once 'config/koneksi.php';

class BarangModel {
    private $conn;
    private $table = 'barang';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT b.*, k.nama_kategori FROM " . $this->table . " b LEFT JOIN kategori_barang k ON b.id_kategori = k.id_kategori ORDER BY b.nama_barang ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT b.*, k.nama_kategori FROM " . $this->table . " b LEFT JOIN kategori_barang k ON b.id_kategori = k.id_kategori WHERE b.id_barang = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($kode_barang, $nama_barang, $id_kategori, $satuan, $harga_beli, $harga_jual, $stok) {
        $query = "INSERT INTO " . $this->table . " SET 
                    kode_barang = :kode_barang,
                    nama_barang = :nama_barang,
                    id_kategori = :id_kategori,
                    satuan = :satuan,
                    harga_beli = :harga_beli,
                    harga_jual = :harga_jual,
                    stok = :stok";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':id_kategori', $id_kategori);
        $stmt->bindParam(':satuan', $satuan);
        $stmt->bindParam(':harga_beli', $harga_beli);
        $stmt->bindParam(':harga_jual', $harga_jual);
        $stmt->bindParam(':stok', $stok);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($id, $kode_barang, $nama_barang, $id_kategori, $satuan, $harga_beli, $harga_jual, $stok) {
        $query = "UPDATE " . $this->table . " SET 
                    kode_barang = :kode_barang,
                    nama_barang = :nama_barang,
                    id_kategori = :id_kategori,
                    satuan = :satuan,
                    harga_beli = :harga_beli,
                    harga_jual = :harga_jual,
                    stok = :stok
                  WHERE id_barang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':id_kategori', $id_kategori);
        $stmt->bindParam(':satuan', $satuan);
        $stmt->bindParam(':harga_beli', $harga_beli);
        $stmt->bindParam(':harga_jual', $harga_jual);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_barang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function existsByKode($kode_barang, $id_barang = null) {
        if ($id_barang) {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE kode_barang = :kode_barang AND id_barang != :id_barang";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE kode_barang = :kode_barang";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_barang', $kode_barang);
        
        if ($id_barang) {
            $stmt->bindParam(':id_barang', $id_barang);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getKategoriAll() {
        $query = "SELECT * FROM kategori_barang ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>