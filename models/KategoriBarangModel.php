<?php
require_once 'config/koneksi.php';

class KategoriBarangModel {
    private $conn;
    private $table = 'kategori_barang';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_kategori = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nama_kategori) {
        $query = "INSERT INTO " . $this->table . " SET nama_kategori = :nama_kategori";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kategori', $nama_kategori);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($id, $nama_kategori) {
        $query = "UPDATE " . $this->table . " SET nama_kategori = :nama_kategori WHERE id_kategori = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kategori', $nama_kategori);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_kategori = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function existsByName($nama_kategori, $id_kategori = null) {
        if ($id_kategori) {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_kategori = :nama_kategori AND id_kategori != :id_kategori";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_kategori = :nama_kategori";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kategori', $nama_kategori);
        
        if ($id_kategori) {
            $stmt->bindParam(':id_kategori', $id_kategori);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>