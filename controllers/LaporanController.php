<?php
require_once 'models/LaporanModel.php';

class LaporanController {
    private $model;

    public function __construct() {
        $this->model = new LaporanModel();
    }

    private function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    private function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function requireAdmin() {
        $this->requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak';
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
    }

    public function index() {
        $this->requireLogin();

        $data = [
            'judul' => 'Laporan'
        ];

        extract($data);
        require_once 'views/laporan/index.php';
    }

    public function getLaporanPenjualan() {
        $this->requireLogin();

        header('Content-Type: application/json');

        try {
            $params = [
                'jenis_filter' => $_POST['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_POST['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_POST['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_POST['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_POST['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_POST['tahun_akhir'] ?? date('Y')
            ];

            $data = $this->model->getLaporanPenjualanTable($params);

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getLaporanPembelian() {
        $this->requireLogin();

        header('Content-Type: application/json');

        try {
            $params = [
                'jenis_filter' => $_POST['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_POST['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_POST['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_POST['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_POST['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_POST['tahun_akhir'] ?? date('Y')
            ];

            $data = $this->model->getLaporanPembelianTable($params);

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getLaporanStokBarang() {
        $this->requireLogin();

        header('Content-Type: application/json');

        try {
            $params = [
                'jenis_stok' => $_POST['jenis_stok'] ?? 'semua',
                'batas_stok' => $_POST['batas_stok'] ?? 5
            ];

            $data = $this->model->getLaporanStokBarangTable($params);

            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cetakPdfPenjualan() {
        $this->requireLogin();

        // Load TCPDF
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }

        try {
            $params = [
                'jenis_filter' => $_GET['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_GET['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_GET['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_GET['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_GET['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_GET['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_GET['tahun_akhir'] ?? date('Y')
            ];

            // Cek apakah ini filter tahunan
            if ($params['jenis_filter'] === 'range-tahun') {
                $this->cetakPdfPenjualanTahunan($params);
                return;
            }

            $data = $this->model->getLaporanPenjualanTable($params);

            // Create PDF with better layout
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(20, 40, 20);
            $pdf->SetAutoPageBreak(TRUE, 30);

            // Set font
            $pdf->SetFont('times', '', 10);

            // Add page
            $pdf->AddPage();

            // Gunakan fungsi header formal
            $tableY = $this->createFormalHeader($pdf, 'LAPORAN PENJUALAN BARANG', 22);

            // Periode (di bawah judul)
            $periode = $this->getPeriodeText($params);
            $pdf->SetY($tableY);
            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(0, 5, 'Periode: ' . $periode, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

            // Pindah ke bawah untuk tabel
            $pdf->SetY($tableY + 12);

            // Main table - Format yang lebih rapi dan sederhana
            $html = '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th align="center" width="4%">No</th>';
            $html .= '<th align="center">No Faktur</th>';
            $html .= '<th align="center">Tanggal</th>';
            $html .= '<th align="center">Kode Barang</th>';
            $html .= '<th>Nama Barang</th>';
            $html .= '<th>Nama Pembeli</th>';
            $html .= '<th align="center">Jumlah</th>';
            $html .= '<th align="right">Harga Satuan</th>';
            $html .= '<th align="right">Subtotal</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (!empty($data)) {
                $no = 1;
                $total_penjualan = 0;

                foreach ($data as $row) {
                    $subtotal = $row['jumlah'] * $row['harga_satuan'];
                    $total_penjualan += $subtotal;

                    $html .= '<tr>';
                    $html .= '<td align="center" width="4%" >' . $no++ . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['no_faktur']) . '</td>';
                    $html .= '<td align="center">' . $this->formatTanggal($row['tanggal']) . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['kode_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_pelanggan'] ?? 'Umum') . '</td>';
                    $html .= '<td align="center">' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
                    $html .= '<td align="right">Rp ' . number_format($row['harga_satuan'], 0, ',', '.') . '</td>';
                    $html .= '<td align="right">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                    $html .= '</tr>';
                }

                // Total row
                $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
                $html .= '<td colspan="8" align="right">Total Keseluruhan</td>';
                $html .= '<td align="right">Rp ' . number_format($total_penjualan, 0, ',', '.') . '</td>';
                $html .= '</tr>';

            } else {
                $html .= '<tr>';
                $html .= '<td colspan="9" align="center" style="padding: 20px; font-style: italic;">';
                $html .= 'Tidak ada data penjualan untuk periode yang dipilih';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Add signature
            $this->addFormalSignature($pdf);

            $filename = 'laporan_penjualan_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'I');

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function cetakPdfPenjualanTahunan($params) {
        $tahun_mulai = $params['tahun_mulai'];
        $tahun_akhir = $params['tahun_akhir'];

        // Create PDF with better layout
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(20, 40, 20);
        $pdf->SetAutoPageBreak(TRUE, 30);

        // Set font
        $pdf->SetFont('times', '', 10);

        // Add page
        $pdf->AddPage();

        // Header style yang formal
        $y_text = 22;

        // TOKO GANI AGRO TANI
        $pdf->SetY($y_text);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 6, 'TOKO GANI AGRO TANI', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Alamat
        $pdf->SetY($y_text + 8);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman, Sumatera Barat', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Garis pembatas
        $pdf->SetY($y_text + 14);
        $pdf->Cell(0, 0.5, '', 'T', 1, 'C');

        // Laporan Penjualan Barang Tahunan
        $pdf->SetY($y_text + 18);
        $pdf->SetFont('times', 'B', 16);
        $pdf->Cell(0, 8, 'LAPORAN PENJUALAN BARANG TAHUNAN', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Periode (di bawah judul)
        $periode = ($tahun_mulai == $tahun_akhir) ? $tahun_mulai : $tahun_mulai . ' - ' . $tahun_akhir;
        $pdf->SetY($y_text + 26);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(0, 5, 'Periode Tahun: ' . $periode, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Pindah ke bawah untuk tabel
        $pdf->SetY($y_text + 38);

        // Generate data untuk setiap tahun
        $tahun_list = [];
        for ($tahun = $tahun_mulai; $tahun <= $tahun_akhir; $tahun++) {
            $tahun_list[] = $tahun;
        }

        foreach ($tahun_list as $tahun) {
            // Ambil data penjualan per tahun
            $tahunan_params = [
                'jenis_filter' => 'range-tahun',
                'tahun_mulai' => $tahun,
                'tahun_akhir' => $tahun
            ];

            $tahunan_data = $this->model->getLaporanPenjualanTable($tahunan_params);

            // Kelompokkan data per barang
            $barang_data = [];
            $total_all = 0;

            if (!empty($tahunan_data)) {
                foreach ($tahunan_data as $row) {
                    $key = $row['kode_barang'];
                    if (!isset($barang_data[$key])) {
                        $barang_data[$key] = [
                            'kode_barang' => $row['kode_barang'],
                            'nama_barang' => $row['nama_barang'],
                            'bulan' => array_fill(1, 12, 0),
                            'total' => 0
                        ];
                    }

                    $bulan = date('n', strtotime($row['tanggal']));
                    $jumlah = $row['jumlah'];
                    $barang_data[$key]['bulan'][$bulan] += $jumlah;
                    $barang_data[$key]['total'] += $jumlah;
                    $total_all += $jumlah;
                }
            }

            // Header tabel tahunan dengan merge cell
            $html = '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 20px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th align="center" rowspan="2">No</th>';
            $html .= '<th align="center" rowspan="2">Kode Barang</th>';
            $html .= '<th rowspan="2">Nama Barang</th>';
            $html .= '<th align="center" colspan="12">Penjualan per Bulan</th>';
            $html .= '<th align="center" rowspan="2">Total</th>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';

            // Header bulan
            $bulan_nama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($bulan_nama as $bulan) {
                $html .= '<th align="center">' . $bulan . '</th>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (!empty($barang_data)) {
                $no = 1;
                foreach ($barang_data as $barang) {
                    $html .= '<tr>';
                    $html .= '<td align="center">' . $no++ . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($barang['kode_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($barang['nama_barang']) . '</td>';

                    // Data per bulan
                    for ($i = 1; $i <= 12; $i++) {
                        $html .= '<td align="center">' . $barang['bulan'][$i] . '</td>';
                    }

                    // Total
                    $html .= '<td align="center" style="background-color: #e9ecef; font-weight: bold;">' . $barang['total'] . '</td>';
                    $html .= '</tr>';
                }

                // Grand total row
                $html .= '<tr style="background-color: #d4edda; font-weight: bold;">';
                $html .= '<td colspan="3" align="right">GRAND TOTAL</td>';

                // Total per bulan
                for ($i = 1; $i <= 12; $i++) {
                    $bulan_total = 0;
                    foreach ($barang_data as $barang) {
                        $bulan_total += $barang['bulan'][$i];
                    }
                    $html .= '<td align="center">' . $bulan_total . '</td>';
                }

                $html .= '<td align="center">' . $total_all . '</td>';
                $html .= '</tr>';

            } else {
                // Tampilkan baris dengan "Tidak Ada Data" di kolom nama barang
                $html .= '<tr>';
                $html .= '<td align="center">-</td>';
                $html .= '<td align="center">-</td>';
                $html .= '<td style="font-style: italic; color: #666;">Tidak Ada Data</td>';

                // Isi semua kolom bulan dengan 0
                for ($i = 1; $i <= 12; $i++) {
                    $html .= '<td align="center">0</td>';
                }

                $html .= '<td align="center">0</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Jika bukan tahun terakhir, tambah halaman baru
            if ($tahun < $tahun_akhir) {
                $pdf->AddPage();
            }
        }

        // Add signature dengan jarak yang disesuaikan
        if ($tahun_mulai == $tahun_akhir) {
            // Hanya satu tahun, signature di halaman yang sama
            $this->addFormalSignature($pdf, false); // false = tidak usah page break
        } else {
            // Multi tahun, biarkan sistem menentukan perlu page break atau tidak
            $this->addFormalSignature($pdf);
        }

        $filename = 'laporan_penjualan_tahunan_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
    }

    public function cetakPdfPembelian() {
        $this->requireLogin();

        // Load TCPDF
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }

        try {
            $params = [
                'jenis_filter' => $_GET['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_GET['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_GET['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_GET['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_GET['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_GET['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_GET['tahun_akhir'] ?? date('Y')
            ];

            // Check if this is yearly report
            if ($params['jenis_filter'] === 'range-tahun') {
                $this->cetakPdfPembelianTahunan($params);
                return;
            }

            $data = $this->model->getLaporanPembelianTable($params);

            // Create PDF with better layout
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(20, 40, 20);
            $pdf->SetAutoPageBreak(TRUE, 30);

            // Set font
            $pdf->SetFont('times', '', 10);

            // Add page
            $pdf->AddPage();

            // Gunakan fungsi header formal
            $tableY = $this->createFormalHeader($pdf, 'LAPORAN PEMBELIAN BARANG', 22);

            // Periode (di bawah judul)
            $periode = $this->getPeriodeText($params);
            $pdf->SetY($tableY);
            $pdf->SetFont('times', 'B', 10);
            $pdf->Cell(0, 5, 'Periode: ' . $periode, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

            // Pindah ke bawah untuk tabel
            $pdf->SetY($tableY + 12);

            // Main table - Format yang lebih rapi dan sederhana
            $html = '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th align="center" width="4%">No</th>';
            $html .= '<th align="center">No Nota</th>';
            $html .= '<th align="center">Tanggal</th>';
            $html .= '<th>Nama Supplier</th>';
            $html .= '<th align="center">Kode Barang</th>';
            $html .= '<th>Nama Barang</th>';
            $html .= '<th align="center">Jumlah</th>';
            $html .= '<th align="right">Harga Beli</th>';
            $html .= '<th align="right">Subtotal</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (!empty($data)) {
                $no = 1;
                $total_pembelian = 0;

                foreach ($data as $row) {
                    $subtotal = $row['jumlah'] * $row['harga_beli'];
                    $total_pembelian += $subtotal;

                    $html .= '<tr>';
                    $html .= '<td align="center" width="4%" >' . $no++ . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['no_nota']) . '</td>';
                    $html .= '<td align="center">' . $this->formatTanggal($row['tanggal']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_supplier']) . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['kode_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    $html .= '<td align="center">' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
                    $html .= '<td align="right">Rp ' . number_format($row['harga_beli'], 0, ',', '.') . '</td>';
                    $html .= '<td align="right">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                    $html .= '</tr>';
                }

                // Total row
                $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
                $html .= '<td colspan="8" align="right">Total Keseluruhan</td>';
                $html .= '<td align="right">Rp ' . number_format($total_pembelian, 0, ',', '.') . '</td>';
                $html .= '</tr>';

            } else {
                $html .= '<tr>';
                $html .= '<td colspan="9" align="center" style="padding: 20px; font-style: italic;">';
                $html .= 'Tidak ada data pembelian untuk periode yang dipilih';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Add signature
            $this->addFormalSignature($pdf);

            $filename = 'laporan_pembelian_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'I');

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function cetakPdfPembelianTahunan($params) {
        $this->requireLogin();

        // Load TCPDF
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }

        // Create PDF
        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(20, 40, 20);
        $pdf->SetAutoPageBreak(TRUE, 30);

        // Set font
        $pdf->SetFont('times', '', 10);

        // Add page
        $pdf->AddPage();

        // Header style yang formal
        $center_x = 148.5; // Tengah halaman A4 Landscape (297/2)
        $y_start = 20;
        $y_text = $y_start + 2;

        // TOKO GANI AGRO TANI
        $pdf->SetY($y_text);
        $pdf->SetFont('times', '', 14);
        $pdf->Cell(0, 6, 'TOKO GANI AGRO TANI', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Alamat
        $pdf->SetY($y_text + 6);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman, Sumatera Barat', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Garis pembatas
        $pdf->SetY($y_text + 13);
        $pdf->Cell(0, 0.5, '', 'T', 1, 'C');

        // Laporan Pembelian Barang (di bawah garis)
        $pdf->SetY($y_text + 18);
        $pdf->SetFont('times', 'B', 16);
        $pdf->Cell(0, 8, 'LAPORAN PEMBELIAN BARANG', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Periode (di bawah judul)
        $periode = 'Tahun ' . $params['tahun_mulai'];
        if ($params['tahun_mulai'] != $params['tahun_akhir']) {
            $periode = 'Tahun ' . $params['tahun_mulai'] . ' - ' . $params['tahun_akhir'];
        }
        $pdf->SetY($y_text + 26);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(0, 5, 'Periode: ' . $periode, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Pindah ke bawah untuk tabel
        $pdf->SetY($y_text + 38);

        $tahun_mulai = (int)$params['tahun_mulai'];
        $tahun_akhir = (int)$params['tahun_akhir'];

        for ($tahun = $tahun_mulai; $tahun <= $tahun_akhir; $tahun++) {
            // Ambil data untuk tahun ini
            $tahun_params = [
                'jenis_filter' => 'range-tahun',
                'tahun_mulai' => $tahun,
                'tahun_akhir' => $tahun
            ];

            $data = $this->model->getLaporanPembelianTable($tahun_params);

            // Group data by barang dan bulan
            $barang_data = [];
            $total_all = 0;

            if (!empty($data)) {
                foreach ($data as $row) {
                    $bulan = (int)date('n', strtotime($row['tanggal']));
                    $key = $row['kode_barang'] . '|' . $row['nama_barang'];

                    if (!isset($barang_data[$key])) {
                        $barang_data[$key] = [
                            'kode_barang' => $row['kode_barang'],
                            'nama_barang' => $row['nama_barang'],
                            'bulan' => array_fill(1, 12, 0),
                            'total' => 0
                        ];
                    }

                    $barang_data[$key]['bulan'][$bulan] += $row['jumlah'];
                    $barang_data[$key]['total'] += $row['jumlah'];
                    $total_all += $row['jumlah'];
                }
            }

            // Table untuk tahun ini dengan merge cell
            $html = '<h3 style="text-align: center; margin-bottom: 10px;">Tahun ' . $tahun . '</h3>';
            $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 9px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th align="center" rowspan="2">No</th>';
            $html .= '<th align="center" rowspan="2">Kode Barang</th>';
            $html .= '<th rowspan="2">Nama Barang</th>';
            $html .= '<th align="center" colspan="12">Pembelian per Bulan</th>';
            $html .= '<th align="center" rowspan="2">Total</th>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';

            // Header untuk setiap bulan
            for ($i = 1; $i <= 12; $i++) {
                $nama_bulan = date('F', mktime(0, 0, 0, $i, 1, 2000));
                $html .= '<th align="center">' . substr($nama_bulan, 0, 3) . '</th>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (!empty($barang_data)) {
                $no = 1;
                foreach ($barang_data as $barang) {
                    $html .= '<tr>';
                    $html .= '<td align="center">' . $no++ . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($barang['kode_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($barang['nama_barang']) . '</td>';

                    // Data per bulan
                    for ($i = 1; $i <= 12; $i++) {
                        $jumlah = $barang['bulan'][$i];
                        $html .= '<td align="center">' . $jumlah . '</td>';
                    }

                    // Total
                    $html .= '<td align="center" style="background-color: #e9ecef; font-weight: bold;">' . $barang['total'] . '</td>';
                    $html .= '</tr>';
                }

                // Grand total row
                $html .= '<tr style="background-color: #d4edda; font-weight: bold;">';
                $html .= '<td colspan="3" align="right">GRAND TOTAL</td>';

                // Total per bulan
                for ($i = 1; $i <= 12; $i++) {
                    $bulan_total = 0;
                    foreach ($barang_data as $barang) {
                        $bulan_total += $barang['bulan'][$i];
                    }
                    $html .= '<td align="center">' . $bulan_total . '</td>';
                }

                $html .= '<td align="center">' . $total_all . '</td>';
                $html .= '</tr>';

            } else {
                // Tampilkan baris dengan "Tidak Ada Data" di kolom nama barang
                $html .= '<tr>';
                $html .= '<td align="center">-</td>';
                $html .= '<td align="center">-</td>';
                $html .= '<td style="font-style: italic; color: #666;">Tidak Ada Data</td>';

                // Isi semua kolom bulan dengan 0
                for ($i = 1; $i <= 12; $i++) {
                    $html .= '<td align="center">0</td>';
                }

                $html .= '<td align="center">0</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Jika bukan tahun terakhir, tambah halaman baru
            if ($tahun < $tahun_akhir) {
                $pdf->AddPage();
            }
        }

        // Add signature dengan jarak yang disesuaikan
        if ($tahun_mulai == $tahun_akhir) {
            // Hanya satu tahun, signature di halaman yang sama
            $this->addFormalSignature($pdf, false); // false = tidak usah page break
        } else {
            // Multi tahun, biarkan sistem menentukan perlu page break atau tidak
            $this->addFormalSignature($pdf);
        }

        $filename = 'laporan_pembelian_tahunan_' . date('Y-m_d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'I');
    }

    public function cetakPdfStokBarang() {
        $this->requireLogin();

        // Load TCPDF
        if (!class_exists('TCPDF')) {
            require_once 'vendor/autoload.php';
        }

        try {
            $params = [
                'jenis_stok' => $_GET['jenis_stok'] ?? 'semua',
                'batas_stok' => $_GET['batas_stok'] ?? 5
            ];

            $data = $this->model->getLaporanStokBarangTable($params);

            // Create PDF with better layout
            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(20, 40, 20);
            $pdf->SetAutoPageBreak(TRUE, 30);

            // Set font
            $pdf->SetFont('times', '', 10);

            // Add page
            $pdf->AddPage();

            // Gunakan fungsi header sederhana
            $tableY = $this->createSimpleHeader($pdf, 'LAPORAN STOK BARANG', 22, 18);

            // Pindah ke bawah untuk tabel
            $pdf->SetY($tableY);

            // Main table - Format yang lebih rapi dan sederhana
            $html = '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
            $html .= '<th align="center" width="4%">No</th>';
            $html .= '<th align="center">Kode Barang</th>';
            $html .= '<th>Kategori</th>';
            $html .= '<th align="center">Satuan</th>';
            $html .= '<th align="center">Stok Tersedia</th>';
            $html .= '<th align="center">Status Barang</th>';
            $html .= '<th>Nama Barang</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (!empty($data)) {
                $no = 1;
                $stok_habis = 0;
                $stok_rendah = 0;
                $stok_aman = 0;

                foreach ($data as $row) {
                    $status = $this->getStatusStok($row['stok'], $params['batas_stok']);

                    // Count stock categories
                    if ($row['stok'] == 0) $stok_habis++;
                    elseif ($row['stok'] <= $params['batas_stok']) $stok_rendah++;
                    else $stok_aman++;

                    $html .= '<tr>';
                    $html .= '<td align="center" width="4%" >' . $no++ . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['kode_barang']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_kategori']) . '</td>';
                    $html .= '<td align="center">' . htmlspecialchars($row['satuan']) . '</td>';
                    $html .= '<td align="center">' . number_format($row['stok'], 0, ',', '.') . '</td>';
                    $html .= '<td align="center">' . $status . '</td>';
                    $html .= '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    $html .= '</tr>';
                }

                // Summary row
                $html .= '<tr style="background-color: #f0f0f0; font-weight: bold;">';
                $html .= '<td colspan="4" align="right">Ringkasan Stok:</td>';
                $html .= '<td align="center">Habis: ' . $stok_habis . '</td>';
                $html .= '<td align="center">Rendah: ' . $stok_rendah . '</td>';
                $html .= '<td align="center">Total: ' . ($stok_habis + $stok_rendah + $stok_aman) . '</td>';
                $html .= '</tr>';

            } else {
                $html .= '<tr>';
                $html .= '<td colspan="7" align="center" style="padding: 20px; font-style: italic;">';
                $html .= 'Tidak ada data barang untuk kriteria yang dipilih';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Add signature
            $this->addFormalSignature($pdf);

            $filename = 'laporan_stok_barang_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'I');

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function exportExcelPenjualan() {
        $this->requireLogin();

        try {
            $params = [
                'jenis_filter' => $_GET['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_GET['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_GET['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_GET['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_GET['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_GET['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_GET['tahun_akhir'] ?? date('Y')
            ];

            $data = $this->model->getLaporanPenjualanTable($params);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_penjualan_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            echo '<table border="1">';
            echo '<tr><th colspan="9">LAPORAN PENJUALAN BARANG</th></tr>';
            echo '<tr><th colspan="9">Periode: ' . $this->getPeriodeText($params) . '</th></tr>';
            echo '<tr><th>No</th><th>No Faktur</th><th>Tanggal</th><th>Kode Barang</th><th>Nama Barang</th><th>Nama Pembeli</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th></tr>';

            if (!empty($data)) {
                $no = 1;
                $total_penjualan = 0;

                foreach ($data as $row) {
                    $subtotal = $row['jumlah'] * $row['harga_satuan'];
                    $total_penjualan += $subtotal;

                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['no_faktur']) . '</td>';
                    echo '<td>' . $this->formatTanggal($row['tanggal']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['kode_barang']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_pelanggan'] ?? 'Umum') . '</td>';
                    echo '<td>' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
                    echo '<td>Rp ' . number_format($row['harga_satuan'], 0, ',', '.') . '</td>';
                    echo '<td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                    echo '</tr>';
                }

                echo '<tr><td colspan="8" align="right"><strong>Total Keseluruhan</strong></td><td><strong>Rp ' . number_format($total_penjualan, 0, ',', '.') . '</strong></td></tr>';
            }

            echo '</table>';

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function exportExcelPembelian() {
        $this->requireLogin();

        try {
            $params = [
                'jenis_filter' => $_GET['jenis_filter'] ?? 'harian',
                'tanggal_mulai' => $_GET['tanggal_mulai'] ?? date('Y-m-d'),
                'tanggal_akhir' => $_GET['tanggal_akhir'] ?? date('Y-m-d'),
                'bulan_mulai' => $_GET['bulan_mulai'] ?? date('Y-m'),
                'bulan_akhir' => $_GET['bulan_akhir'] ?? date('Y-m'),
                'tahun_mulai' => $_GET['tahun_mulai'] ?? date('Y'),
                'tahun_akhir' => $_GET['tahun_akhir'] ?? date('Y')
            ];

            $data = $this->model->getLaporanPembelianTable($params);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_pembelian_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            echo '<table border="1">';
            echo '<tr><th colspan="9">LAPORAN PEMBELIAN BARANG</th></tr>';
            echo '<tr><th colspan="9">Periode: ' . $this->getPeriodeText($params) . '</th></tr>';
            echo '<tr><th>No</th><th>No Nota</th><th>Tanggal</th><th>Nama Supplier</th><th>Kode Barang</th><th>Nama Barang</th><th>Jumlah</th><th>Harga Beli</th><th>Subtotal</th></tr>';

            if (!empty($data)) {
                $no = 1;
                $total_pembelian = 0;

                foreach ($data as $row) {
                    $subtotal = $row['jumlah'] * $row['harga_beli'];
                    $total_pembelian += $subtotal;

                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['no_nota']) . '</td>';
                    echo '<td>' . $this->formatTanggal($row['tanggal']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_supplier']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['kode_barang']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    echo '<td>' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
                    echo '<td>Rp ' . number_format($row['harga_beli'], 0, ',', '.') . '</td>';
                    echo '<td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                    echo '</tr>';
                }

                echo '<tr><td colspan="8" align="right"><strong>Total Keseluruhan</strong></td><td><strong>Rp ' . number_format($total_pembelian, 0, ',', '.') . '</strong></td></tr>';
            }

            echo '</table>';

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function exportExcelStokBarang() {
        $this->requireLogin();

        try {
            $params = [
                'jenis_stok' => $_GET['jenis_stok'] ?? 'semua',
                'batas_stok' => $_GET['batas_stok'] ?? 5
            ];

            $data = $this->model->getLaporanStokBarangTable($params);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_stok_barang_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            echo '<table border="1">';
            echo '<tr><th colspan="7">LAPORAN STOK BARANG</th></tr>';
            echo '<tr><th colspan="7">' . $this->getJenisStokText($params['jenis_stok'], $params['batas_stok']) . '</th></tr>';
            echo '<tr><th>No</th><th>Kode Barang</th><th>Kategori</th><th>Satuan</th><th>Stok Tersedia</th><th>Status Barang</th><th>Nama Barang</th></tr>';

            if (!empty($data)) {
                $no = 1;

                foreach ($data as $row) {
                    $status = $this->getStatusStok($row['stok'], $params['batas_stok']);

                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['kode_barang']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_kategori']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['satuan']) . '</td>';
                    echo '<td>' . number_format($row['stok'], 0, ',', '.') . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                    echo '</tr>';
                }
            }

            echo '</table>';

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function getProfessionalHeader($judul, $periode) {
        $html = '<div style="text-align: center; margin-bottom: 25px; font-family: Arial, sans-serif;">';

        // Header perusahaan
        $html .= '<h1 style="margin: 0; font-size: 20px; color: #000; font-weight: bold;">TOKO GANI AGRO TANI</h1>';
        $html .= '<p style="margin: 2px 0; font-size: 10px; color: #666;">Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman</p>';
        $html .= '<p style="margin: 2px 0; font-size: 10px; color: #666;">Padang Pariaman, Sumatera Barat</p>';

        // Garis pembatas
        $html .= '<hr style="border: 1px solid #000; margin: 8px 0;">';

        // Judul laporan
        $html .= '<h2 style="margin: 10px 0 3px 0; font-size: 16px; color: #000; text-transform: uppercase; font-weight: bold;">' . $judul . '</h2>';
        $html .= '<h3 style="margin: 0 0 15px 0; font-size: 12px; color: #333;">Periode: ' . $periode . '</h3>';

        $html .= '</div>';

        return $html;
    }

    private function addFormalSignature($pdf, $allowPageBreak = true) {
        // Get page height and current Y position to determine if we need a new page
        $pageHeight = $pdf->getPageHeight();
        $currentY = $pdf->GetY();
        $pageMargin = 20; // Standard margin
        $usablePageHeight = $pageHeight - (2 * $pageMargin);
        $tableThreshold = 0.7 * $usablePageHeight; // 70% of usable page height

        // Check if table content exceeds 70% of the page and page break is allowed
        if ($allowPageBreak && $currentY > $tableThreshold) {
            // Add new page for signature
            $pdf->AddPage();
            $startY = 120; // Start from a standard position on the new page
        } else {
            // Add spacing below the table on the same page
            $startY = $currentY + 20; // Add 20 units spacing
        }

        // Format tanggal formal Indonesia
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $tanggal = date('d');
        $nama_bulan = $bulan[date('n') - 1];
        $tahun = date('Y');
        $tempatTanggal = "Padang Pariaman, $tanggal $nama_bulan $tahun";

        // Tentukan posisi X berdasarkan ukuran halaman
        $pageWidth = $pdf->getPageWidth();
        $startX = ($pageWidth > 210) ? 200 : 110;  // Landscape (>210): 200, Portrait: 110

        // 1. Tempat dan Tanggal - menggunakan koordinat spesifik
        $pdf->SetFont('times', '', 11);
        $pdf->SetXY($startX, $startY);
        $pdf->Cell(70, 6, $tempatTanggal, 0, 0, 'L');

        // 2. Jabatan Penandatangan - menggunakan koordinat spesifik
        $pdf->SetFont('times', 12);
        $pdf->SetXY($startX, $startY + -3 + 10);
        $pdf->MultiCell(70, 5, 'Pemilik Gani Agro Tani ', 0, 'L');

        // 3. Nama - menggunakan koordinat spesifik (ambil dari URL parameter)
        $nama = $_GET['nama_pimpinan'] ?? $_SESSION['nama_lengkap'] ?? 'Pemilik';
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetXY($startX, $startY + 6 + 10 + 15);
        $pdf->Cell(70, 6, strtoupper($nama), 0, 0, 'L');
    }

    private function getProfessionalSignature() {
        $nama = $_SESSION['nama_lengkap'] ?? 'Pimpinan';
        $tanggal = date('d F Y');

        $html = '<div style="margin-top: 60px; font-family: Arial, sans-serif;">';
        $html .= '<div style="float: right; text-align: center; width: 200px;">';
        $html .= '<p style="margin: 0; font-size: 10px;">Padang Pariaman, ' . $tanggal . '</p>';
        $html .= '<p style="margin: 40px 0 5px 0; font-size: 10px;"><strong>Pimpinan</strong></p>';
        $html .= '<p style="margin: 0; font-size: 10px;">_________________________</p>';
        $html .= '<p style="margin: 5px 0 0 0; font-size: 10px; font-weight: bold;">' . htmlspecialchars($nama) . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div style="clear: both;"></div>';

        return $html;
    }

    private function getLaporanHeader($judul, $periode) {
        return $this->getProfessionalHeader($judul, $periode);
    }

    private function getSignature() {
        return $this->getProfessionalSignature();
    }

    private function getPeriodeText($params) {
        switch ($params['jenis_filter']) {
            case 'harian':
                return $this->formatTanggal($params['tanggal_mulai']);
            case 'range-hari':
                return $this->formatTanggal($params['tanggal_mulai']) . ' - ' . $this->formatTanggal($params['tanggal_akhir']);
            case 'range-bulan':
                return $this->formatBulanTahun($params['bulan_mulai']) . ' - ' . $this->formatBulanTahun($params['bulan_akhir']);
            case 'range-tahun':
                return $params['tahun_mulai'] . ' - ' . $params['tahun_akhir'];
            default:
                return $this->formatTanggal(date('Y-m-d'));
        }
    }

    private function getJenisStokText($jenis_stok, $batas_stok) {
        switch ($jenis_stok) {
            case 'habis':
                return 'Stok Barang Habis';
            case 'rendah':
                return 'Stok Barang Rendah (Batas: ' . $batas_stok . ')';
            case 'aman':
                return 'Stok Barang Aman';
            default:
                return 'Semua Stok Barang';
        }
    }

    private function getStatusStok($stok, $batas_stok) {
        if ($stok == 0) {
            return 'HABIS';
        } elseif ($stok <= $batas_stok) {
            return 'RENDAH';
        } else {
            return 'AMAN';
        }
    }

    private function getStatusStokStyle($stok, $batas_stok) {
        if ($stok == 0) {
            return 'background-color:#f8d7da; color:#721c24; font-weight:bold;';
        } elseif ($stok <= $batas_stok) {
            return 'background-color:#fff3cd; color:#856404; font-weight:bold;';
        } else {
            return 'background-color:#d1ecf1; color:#0c5460; font-weight:bold;';
        }
    }

    private function formatTanggal($tanggal) {
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

    private function formatBulanTahun($bulan_tahun) {
        $bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $bln = substr($bulan_tahun, 5, 2);
        $thn = substr($bulan_tahun, 0, 4);

        return $bulan[$bln] . ' ' . $thn;
    }

    /**
     * Fungsi untuk membuat header formal PDF (nama toko + alamat + garis)
     */
    private function createFormalHeader($pdf, $judul = '', $y_position = 22) {
        // Header style yang formal
        $y_text = $y_position;

        // TOKO GANI AGRO TANI
        $pdf->SetY($y_text);
        $pdf->SetFont('times', 'B',24);
        $pdf->Cell(0, 6, 'TOKO GANI AGRO TANI', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Alamat
        $pdf->SetY($y_text + 9);
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 5, 'Jl. Raya Tanjung Basung II, Kec. Batang Anai, Kab. Padang Pariaman, Sumatera Barat', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        // Garis pembatas
        $pdf->SetY($y_text + 15);
        $pdf->Cell(0, 0.5, '', 'T', 1, 'C');

        // Jika ada judul tambahkan setelah garis
        if (!empty($judul)) {
            $pdf->SetY($y_text + 18);
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 8, $judul, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            return $y_text + 28; // Return posisi Y untuk tabel
        }

        return $y_text + 20; // Return posisi Y untuk tabel tanpa judul
    }

    /**
     * Fungsi untuk membuat header sederhana PDF (hanya judul)
     */
    private function createSimpleHeader($pdf, $judul, $y_position = 22, $font_size = 18) {
        $y_text = $y_position;

        // Header sederhana - hanya judul
        $pdf->SetY($y_text);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->Cell(0, 10, $judul, 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        return $y_text + 20; // Return posisi Y untuk tabel
    }
}