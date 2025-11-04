<?php
require_once 'config/koneksi.php';

class AuthModel {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT id_user, username, password, nama_lengkap, role FROM " . $this->table . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Cek apakah password dalam database adalah hash (dimulai dengan $)
            if (substr($row['password'], 0, 1) === '$') {
                // Ini adalah password yang di-hash, gunakan password_verify
                if (password_verify($password, $row['password'])) {
                    return $row;
                }
            } else {
                // Ini adalah password plaintext, cocokkan langsung
                if ($row['password'] === $password) {
                    return $row;
                }
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT id_user, username, nama_lengkap, role FROM " . $this->table . " WHERE id_user = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>