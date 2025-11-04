<?php
// Mulai session
session_start();

// Memuat koneksi database
require_once 'config/koneksi.php';
$database = new Database();

// Menentukan controller dan action default
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Menentukan file controller
$controllerFile = 'controllers/' . ucfirst($controller) . 'Controller.php';

// Memeriksa apakah file controller ada
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Membuat instance controller
    $controllerClass = ucfirst($controller) . 'Controller';
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();

        // Memeriksa apakah method (action) ada di dalam controller
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            // Jika action tidak ditemukan, gunakan action default
            if (method_exists($controllerInstance, 'index')) {
                $controllerInstance->index();
            } else {
                echo "Action not found";
            }
        }
    } else {
        echo "Controller class not found";
    }
} else {
    // Jika controller tidak ditemukan, tampilkan halaman 404
    echo "Controller not found";
}
?>