<?php
// routing_helper.php - Sistem routing tambahan untuk mendukung format camel case
function convertCamelCaseToKebabCase($string) {
    // Mengubah format CamelCase menjadi kebab-case
    return strtolower(preg_replace('/[A-Z]/', '-$0', lcfirst($string)));
}

function getControllerClassFromCamelCase($controllerName) {
    // Konversi camel case ke format yang sesuai untuk pembuatan class
    // Untuk kategoriBarang menjadi KategoriBarang untuk class KategoriBarangController
    return ucfirst($controllerName) . 'Controller';
}

// Fungsi untuk membuat URL dalam format camel case yang bisa dikonversi
function createUrl($controller, $action, $params = []) {
    $url = "index.php?controller=" . $controller . "&action=" . $action;
    foreach ($params as $key => $value) {
        $url .= "&" . $key . "=" . urlencode($value);
    }
    return $url;
}
?>