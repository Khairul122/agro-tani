<?php
// config/tcpdf_config.php
require_once 'vendor/autoload.php';

// Inisialisasi objek TCPDF sebagai variabel global
if (!isset($pdf)) {
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
}
?>