<?php
require_once 'config/koneksi.php';

class PelangganModel {
    private $conn;
    private $table = 'pelanggan';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nama_pelanggan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_pelanggan = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nama_pelanggan, $alamat, $no_hp) {
        $query = "INSERT INTO " . $this->table . " SET 
                    nama_pelanggan = :nama_pelanggan,
                    alamat = :alamat,
                    no_hp = :no_hp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_hp', $no_hp);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($id, $nama_pelanggan, $alamat, $no_hp) {
        $query = "UPDATE " . $this->table . " SET 
                    nama_pelanggan = :nama_pelanggan,
                    alamat = :alamat,
                    no_hp = :no_hp
                  WHERE id_pelanggan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_hp', $no_hp);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_pelanggan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function existsByNama($nama_pelanggan, $id_pelanggan = null) {
        if ($id_pelanggan) {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_pelanggan = :nama_pelanggan AND id_pelanggan != :id_pelanggan";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_pelanggan = :nama_pelanggan";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
        
        if ($id_pelanggan) {
            $stmt->bindParam(':id_pelanggan', $id_pelanggan);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>