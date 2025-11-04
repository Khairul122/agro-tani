<?php
// helpers/laporan_helper.php

/**
 * Fungsi untuk membuat header laporan yang konsisten
 */
function createLaporanHeader($judul, $periode, $orientasi = 'L') {
    // Load TCPDF
    if (!class_exists('TCPDF')) {
        require_once 'vendor/autoload.php';
    }
    
    $pdf = new \TCPDF($orientasi, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Agro Tani');
    $pdf->SetTitle($judul);
    
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
    
    // Add page
    $pdf->AddPage();
    
    // Buat header laporan
    $header_html = '
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-weight: bold; color: #333; margin: 0;">TOKO GANI AGRO TANI</h2>
        <p style="color: #666; margin: 5px 0 0;">Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman</p>
        <hr style="border: 2px solid #333; margin: 10px 0;">
    </div>
    
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="font-weight: bold; color: #333; margin: 0;">' . $judul . '</h3>
        <h4 style="color: #666; margin: 5px 0 0;">Periode: ' . $periode . '</h4>
    </div>';
    
    $pdf->writeHTML($header_html, true, false, true, false, '');
    
    return $pdf;
}

/**
 * Fungsi untuk format tanggal dalam bahasa Indonesia
 */
function formatTanggalIndonesia($tanggal) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    
    return $tgl . ' ' . $bln . ' ' . $thn;
}

/**
 * Fungsi untuk format tanggal bulan-tahun dalam bahasa Indonesia
 */
function formatTanggalIndonesiaBulanTahun($bulan, $tahun) {
    $bulan_nama = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    return $bulan_nama[$bulan] . ' ' . $tahun;
}

/**
 * Fungsi untuk format angka rupiah
 */
function formatRupiah($nominal) {
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

/**
 * Fungsi untuk membuat kolom tengah
 */
function buatKolomTengah($isi) {
    return '<div style="text-align: center;">' . $isi . '</div>';
}

/**
 * Fungsi untuk format jumlah dengan satuan
 */
function formatJumlah($jumlah, $satuan) {
    return $jumlah . ' ' . $satuan;
}
?>