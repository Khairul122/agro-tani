<?php
require_once 'models/AuthModel.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function index() {
        if (isset($_SESSION['user_id'])) {
            // Jika sudah login, redirect ke dashboard
            $role = $_SESSION['role'];
            switch($role) {
                case 'admin':
                    header('Location: index.php?controller=dashboard&action=admin');
                    break;
                case 'kasir':
                    header('Location: index.php?controller=dashboard&action=kasir');
                    break;
                case 'owner':
                    header('Location: index.php?controller=dashboard&action=owner');
                    break;
                default:
                    header('Location: index.php?controller=dashboard&action=index');
            }
            exit();
        }
        
        include 'views/auth/index.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $user = $this->authModel->login($username, $password);

            if ($user) {
                // Set session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                // Redirect berdasarkan role
                switch($user['role']) {
                    case 'admin':
                        echo "<script>alert('Login berhasil! Selamat datang, " . $user['nama_lengkap'] . " (Admin)'); window.location.href='index.php?controller=dashboard&action=admin';</script>";
                        break;
                    case 'kasir':
                        echo "<script>alert('Login berhasil! Selamat datang, " . $user['nama_lengkap'] . " (Kasir)'); window.location.href='index.php?controller=dashboard&action=kasir';</script>";
                        break;
                    case 'owner':
                        echo "<script>alert('Login berhasil! Selamat datang, " . $user['nama_lengkap'] . " (Owner)'); window.location.href='index.php?controller=dashboard&action=owner';</script>";
                        break;
                    default:
                        echo "<script>alert('Login berhasil! Selamat datang, " . $user['nama_lengkap'] . "'); window.location.href='index.php?controller=dashboard&action=index';</script>";
                }
                exit();
            } else {
                // Login gagal
                $error = "Username atau password salah";
                echo "<script>alert('Login gagal! " . $error . "');</script>";
                include_once 'views/auth/index.php';
            }
        }
    }

    public function logout() {
        // Hapus session
        $nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'User';
        session_destroy();
        
        // Tampilkan notifikasi logout dan redirect ke halaman login
        echo "<script>alert('Anda telah logout, " . $nama_lengkap . "!'); window.location.href='index.php?controller=auth&action=index';</script>";
        exit();
    }
}
?>