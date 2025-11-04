<?php
require_once 'config/koneksi.php';

class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT id_user, username, nama_lengkap, role, created_at FROM " . $this->table . " ORDER BY nama_lengkap ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT id_user, username, nama_lengkap, role FROM " . $this->table . " WHERE id_user = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $password, $nama_lengkap, $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO " . $this->table . " SET 
                    username = :username,
                    password = :password,
                    nama_lengkap = :nama_lengkap,
                    role = :role";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':nama_lengkap', $nama_lengkap);
        $stmt->bindParam(':role', $role);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($id, $username, $nama_lengkap, $role, $password = null) {
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table . " SET 
                        username = :username,
                        password = :password,
                        nama_lengkap = :nama_lengkap,
                        role = :role
                      WHERE id_user = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':nama_lengkap', $nama_lengkap);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
        } else {
            $query = "UPDATE " . $this->table . " SET 
                        username = :username,
                        nama_lengkap = :nama_lengkap,
                        role = :role
                      WHERE id_user = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':nama_lengkap', $nama_lengkap);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
        }
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function existsByUsername($username, $id_user = null) {
        if ($id_user) {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE username = :username AND id_user != :id_user";
        } else {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE username = :username";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($id_user) {
            $stmt->bindParam(':id_user', $id_user);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    public function updatePassword($id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>