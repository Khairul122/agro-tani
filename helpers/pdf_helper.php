<?php
// helpers/pdf_helper.php

// Variabel global untuk menyimpan instance TCPDF
$GLOBALS['tcpdf_instance'] = null;

/**
 * Fungsi untuk mendapatkan instance TCPDF
 */
function getPdfInstance() {
    if ($GLOBALS['tcpdf_instance'] === null) {
        // Load TCPDF jika belum di-load
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }
        
        // Buat instance baru TCPDF
        $GLOBALS['tcpdf_instance'] = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }
    
    return $GLOBALS['tcpdf_instance'];
}

/**
 * Fungsi untuk membuat instance TCPDF baru
 */
function createNewPdfInstance() {
    if (!class_exists('TCPDF')) {
        require_once 'vendor/autoload.php';
    }
    
    return new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
}

/**
 * Template dasar untuk laporan
 */
function setupPdf($pdf, $title = 'Laporan', $orientation = PDF_PAGE_ORIENTATION) {
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle($title);
    $pdf->SetHeaderData('', 0, $title, '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();
    
    return $pdf;
}
?>