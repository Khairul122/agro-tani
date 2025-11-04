<?php
require_once 'config/koneksi.php';

class SupplierModel {
    private $conn;
    private $table = 'supplier';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nama_supplier ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_supplier = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nama_supplier, $alamat, $no_hp) {
        $query = "INSERT INTO " . $this->table . " SET 
                    nama_supplier = :nama_supplier,
                    alamat = :alamat,
                    no_hp = :no_hp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_supplier', $nama_supplier);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_hp', $no_hp);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($id, $nama_supplier, $alamat, $no_hp) {
        $query = "UPDATE " . $this->table . " SET 
                    nama_supplier = :nama_supplier,
                    alamat = :alamat,
                    no_hp = :no_hp
                  WHERE id_supplier = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_supplier', $nama_supplier);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_hp', $no_hp);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_supplier = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function existsByNama($nama_supplier, $id_supplier = null) {
        if ($id_supplier) {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_supplier = :nama_supplier AND id_supplier != :id_supplier";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nama_supplier = :nama_supplier";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_supplier', $nama_supplier);
        
        if ($id_supplier) {
            $stmt->bindParam(':id_supplier', $id_supplier);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>