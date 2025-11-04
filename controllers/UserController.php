<?php
require_once 'models/UserModel.php';

class UserController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat mengakses halaman ini.'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $users = $this->model->getAll();
        include 'views/user/index.php';
    }

    public function tambah() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menambah user.'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $nama_lengkap = trim($_POST['nama_lengkap']);
            $role = $_POST['role'];
            
            // Validasi
            if (empty($username) || empty($password) || empty($nama_lengkap) || empty($role)) {
                echo "<script>alert('Semua field wajib diisi!'); window.location.href='index.php?controller=user&action=tambah';</script>";
                exit();
            }
            
            // Validasi role
            if (!in_array($role, ['admin', 'kasir', 'owner'])) {
                echo "<script>alert('Role tidak valid!'); window.location.href='index.php?controller=user&action=tambah';</script>";
                exit();
            }
            
            // Validasi panjang password
            if (strlen($password) < 6) {
                echo "<script>alert('Password minimal 6 karakter!'); window.location.href='index.php?controller=user&action=tambah';</script>";
                exit();
            }
            
            // Cek apakah username sudah ada
            if ($this->model->existsByUsername($username)) {
                echo "<script>alert('Username sudah ada!'); window.location.href='index.php?controller=user&action=tambah';</script>";
                exit();
            }
            
            if ($this->model->create($username, $password, $nama_lengkap, $role)) {
                echo "<script>alert('User berhasil ditambahkan!'); window.location.href='index.php?controller=user&action=index';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan user!'); window.location.href='index.php?controller=user&action=tambah';</script>";
            }
            exit();
        }
        
        include 'views/user/form.php';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat mengedit user.'); window.location.href='index.php?controller=dashboard&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $user = $this->model->getById($id);
        
        if (!$user) {
            echo "<script>alert('User tidak ditemukan!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $nama_lengkap = trim($_POST['nama_lengkap']);
            $role = $_POST['role'];
            $password = $_POST['password'] ?? '';
            
            // Validasi
            if (empty($username) || empty($nama_lengkap) || empty($role)) {
                echo "<script>alert('Username, nama lengkap, dan role wajib diisi!'); window.location.href='index.php?controller=user&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi role
            if (!in_array($role, ['admin', 'kasir', 'owner'])) {
                echo "<script>alert('Role tidak valid!'); window.location.href='index.php?controller=user&action=edit&id=$id';</script>";
                exit();
            }
            
            // Validasi panjang password jika diisi
            if (!empty($password) && strlen($password) < 6) {
                echo "<script>alert('Password minimal 6 karakter!'); window.location.href='index.php?controller=user&action=edit&id=$id';</script>";
                exit();
            }
            
            // Cek apakah username sudah ada (selain user saat ini)
            if ($this->model->existsByUsername($username, $id)) {
                echo "<script>alert('Username sudah ada!'); window.location.href='index.php?controller=user&action=edit&id=$id';</script>";
                exit();
            }
            
            // Panggil update dengan atau tanpa password tergantung apakah password diisi
            if (!empty($password)) {
                $result = $this->model->update($id, $username, $nama_lengkap, $role, $password);
            } else {
                $result = $this->model->update($id, $username, $nama_lengkap, $role);
            }
            
            if ($result) {
                echo "<script>alert('User berhasil diupdate!'); window.location.href='index.php?controller=user&action=index';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate user!'); window.location.href='index.php?controller=user&action=edit&id=$id';</script>";
            }
            exit();
        }
        
        include 'views/user/form.php';
    }

    public function hapus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo "<script>alert('Akses ditolak! Hanya admin yang dapat menghapus user.'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
        
        $id = $_GET['id'] ?? 0;
        $user = $this->model->getById($id);
        
        if (!$user) {
            echo "<script>alert('User tidak ditemukan!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
        
        // Tidak bisa menghapus diri sendiri
        if ($id == $_SESSION['user_id']) {
            echo "<script>alert('Tidak dapat menghapus akun Anda sendiri!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
        
        if ($this->model->delete($id)) {
            echo "<script>alert('User berhasil dihapus!'); window.location.href='index.php?controller=user&action=index';</script>";
        } else {
            echo "<script>alert('Gagal menghapus user!'); window.location.href='index.php?controller=user&action=index';</script>";
        }
        exit();
    }
}
?>