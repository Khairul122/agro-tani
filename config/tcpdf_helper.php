<?php
// config/tcpdf_helper.php

/**
 * Fungsi untuk mendapatkan instance TCPDF
 */
function getTcpdfInstance() {
    static $pdf = null;
    
    if ($pdf === null) {
        // Load TCPDF
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }
        
        // Buat instance TCPDF dengan orientasi landscape
        $pdf = new \TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistem Agro Tani');
        $pdf->SetTitle('Laporan Bisnis');
        $pdf->SetSubject('Laporan Sistem Agro Tani');
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
    }
    
    return $pdf;
}

/**
 * Setup header dan footer untuk laporan
 */
function setupPdfHeaderFooter($pdf, $title) {
    // Header
    $pdf->SetHeaderData('', 0, $title, "Sistem Informasi Toko Gani Agro Tani\nJl. Raya Padang - Pariaman, Sumatera Barat\ninfo@ganiagrotani.com");
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
    // Set header margin
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
}
?>