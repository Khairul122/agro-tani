<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              <div class="page-header">
                <h3 class="page-title">Cetak Laporan</h3>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                  <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" data-bs-toggle="tab" href="#penjualan" role="tab">
                        <i class="mdi mdi-cash-multiple font-size-16"></i>
                        <span class="d-none d-sm-inline-block ms-1">Laporan Penjualan</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#pembelian" role="tab">
                        <i class="mdi mdi-shopping font-size-16"></i>
                        <span class="d-none d-sm-inline-block ms-1">Laporan Pembelian</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#stok" role="tab">
                        <i class="mdi mdi-package-variant-closed font-size-16"></i>
                        <span class="d-none d-sm-inline-block ms-1">Laporan Stok Barang</span>
                      </a>
                    </li>
                  </ul>

                  <div class="tab-content p-3 text-muted">
                    <!-- Tab Penjualan -->
                    <div class="tab-pane active" id="penjualan" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="card border">
                            <div class="card-header bg-light">
                              <h5 class="mb-0">Filter Laporan Penjualan</h5>
                            </div>
                            <div class="card-body">
                              <form id="form-penjualan">
                                <div class="row">
                                  <div class="col-md-2">
                                    <label class="form-label">Jenis Filter</label>
                                    <select class="form-select" id="filter-jenis-penjualan">
                                      <option value="semua">Semua</option>
                                      <!-- <option value="harian">Harian</option> -->
                                      <option value="range-hari">Hari</option>
                                      <option value="range-bulan">Bulan</option>
                                      <option value="range-tahun">Tahun</option>
                                    </select>
                                  </div>

                                  <!-- Filter Harian -->
                                  <div class="col-md-2" id="filter-harian-penjualan">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal-penjualan" value="<?php echo date('Y-m-d'); ?>">
                                  </div>

                                  <!-- Filter Range Hari -->
                                  <div class="col-md-2 d-none" id="filter-range-hari-penjualan">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal-mulai-hari-penjualan" value="<?php echo date('Y-m-d'); ?>">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-hari-akhir-penjualan">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal-akhir-hari-penjualan" value="<?php echo date('Y-m-d'); ?>">
                                  </div>

                                  <!-- Filter Range Bulan -->
                                  <div class="col-md-2 d-none" id="filter-range-bulan-penjualan">
                                    <label class="form-label">Bulan Mulai</label>
                                    <input type="month" class="form-control" id="bulan-mulai-penjualan" value="<?php echo date('Y-m'); ?>">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-bulan-akhir-penjualan">
                                    <label class="form-label">Bulan Akhir</label>
                                    <input type="month" class="form-control" id="bulan-akhir-penjualan" value="<?php echo date('Y-m'); ?>">
                                  </div>

                                  <!-- Filter Range Tahun -->
                                  <div class="col-md-2 d-none" id="filter-range-tahun-penjualan">
                                    <label class="form-label">Tahun Mulai</label>
                                    <input type="number" class="form-control" id="tahun-mulai-penjualan" value="<?php echo date('Y'); ?>" min="2020" max="2030">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-tahun-akhir-penjualan">
                                    <label class="form-label">Tahun Akhir</label>
                                    <input type="number" class="form-control" id="tahun-akhir-penjualan" value="<?php echo date('Y'); ?>" min="2020" max="2030">
                                  </div>

                                  <div class="col-md-3">
                                    <label class="form-label">Nama Pimpinan</label>
                                    <input type="text" class="form-control" id="nama-pimpinan-penjualan" placeholder="Nama pimpinan untuk tanda tangan">
                                  </div>

                                  <div class="col-md-1 d-flex align-items-end">
                                    <div class="btn-group w-100">
                                      <button type="button" class="btn btn-primary" onclick="tampilkanDataPenjualan()">
                                        <i class="mdi mdi-eye"></i> Tampilkan
                                      </button>
                                      <button type="button" class="btn btn-success" onclick="cetakLaporanPenjualan()">
                                        <i class="mdi mdi-printer"></i> PDF
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row mt-3">
                        <div class="col-lg-12">
                          <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-penjualan">
                              <thead class="table-dark">
                                <tr>
                                  <th class="text-center" style="width: 5%">No</th>
                                  <th class="text-center" style="width: 10%">No Faktur</th>
                                  <th class="text-center" style="width: 10%">Tanggal</th>
                                  <th class="text-center" style="width: 10%">Kode Barang</th>
                                  <th style="width: 25%">Nama Barang</th>
                                  <th style="width: 15%">Nama Pembeli</th>
                                  <th class="text-center" style="width: 8%">Jumlah</th>
                                  <th class="text-end" style="width: 12%">Harga Satuan</th>
                                  <th class="text-end" style="width: 15%">Subtotal</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="9" class="text-center text-muted py-4">
                                    <i class="mdi mdi-information-outline font-size-20"></i>
                                    <br>Silakan pilih filter dan klik tombol Tampilkan untuk melihat data
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>

                          <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                              <tbody>
                                <tr class="table-primary fw-bold">
                                  <td class="text-end" colspan="8">Total Keseluruhan</td>
                                  <td class="text-end" id="total-penjualan">Rp 0,00</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Tab Pembelian -->
                    <div class="tab-pane" id="pembelian" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="card border">
                            <div class="card-header bg-light">
                              <h5 class="mb-0">Filter Laporan Pembelian</h5>
                            </div>
                            <div class="card-body">
                              <form id="form-pembelian">
                                <div class="row">
                                  <div class="col-md-2">
                                    <label class="form-label">Jenis Filter</label>
                                    <select class="form-select" id="filter-jenis-pembelian">
                                      <option value="semua">Semua</option>
                                      <!-- <option value="harian">Harian</option> -->
                                      <option value="range-hari">Hari</option>
                                      <option value="range-bulan">Bulan</option>
                                      <option value="range-tahun">Tahun</option>
                                    </select>
                                  </div>

                                  <!-- Filter Harian -->
                                  <div class="col-md-2" id="filter-harian-pembelian">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal-pembelian" value="<?php echo date('Y-m-d'); ?>">
                                  </div>

                                  <!-- Filter Range Hari -->
                                  <div class="col-md-2 d-none" id="filter-range-hari-pembelian">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal-mulai-hari-pembelian" value="<?php echo date('Y-m-d'); ?>">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-hari-akhir-pembelian">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal-akhir-hari-pembelian" value="<?php echo date('Y-m-d'); ?>">
                                  </div>

                                  <!-- Filter Range Bulan -->
                                  <div class="col-md-2 d-none" id="filter-range-bulan-pembelian">
                                    <label class="form-label">Bulan Mulai</label>
                                    <input type="month" class="form-control" id="bulan-mulai-pembelian" value="<?php echo date('Y-m'); ?>">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-bulan-akhir-pembelian">
                                    <label class="form-label">Bulan Akhir</label>
                                    <input type="month" class="form-control" id="bulan-akhir-pembelian" value="<?php echo date('Y-m'); ?>">
                                  </div>

                                  <!-- Filter Range Tahun -->
                                  <div class="col-md-2 d-none" id="filter-range-tahun-pembelian">
                                    <label class="form-label">Tahun Mulai</label>
                                    <input type="number" class="form-control" id="tahun-mulai-pembelian" value="<?php echo date('Y'); ?>" min="2020" max="2030">
                                  </div>
                                  <div class="col-md-2 d-none" id="filter-range-tahun-akhir-pembelian">
                                    <label class="form-label">Tahun Akhir</label>
                                    <input type="number" class="form-control" id="tahun-akhir-pembelian" value="<?php echo date('Y'); ?>" min="2020" max="2030">
                                  </div>

                                  <div class="col-md-3">
                                    <label class="form-label">Nama Pimpinan</label>
                                    <input type="text" class="form-control" id="nama-pimpinan-pembelian" placeholder="Nama pimpinan untuk tanda tangan">
                                  </div>

                                  <div class="col-md-1 d-flex align-items-end">
                                    <div class="btn-group w-100">
                                      <button type="button" class="btn btn-primary" onclick="tampilkanDataPembelian()">
                                        <i class="mdi mdi-eye"></i> Tampilkan
                                      </button>
                                      <button type="button" class="btn btn-success" onclick="cetakLaporanPembelian()">
                                        <i class="mdi mdi-printer"></i> PDF
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row mt-3">
                        <div class="col-lg-12">
                          <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-pembelian">
                              <thead class="table-success">
                                <tr>
                                  <th class="text-center" style="width: 5%">No</th>
                                  <th class="text-center" style="width: 10%">No Nota</th>
                                  <th class="text-center" style="width: 10%">Tanggal</th>
                                  <th style="width: 15%">Nama Supplier</th>
                                  <th class="text-center" style="width: 10%">Kode Barang</th>
                                  <th style="width: 20%">Nama Barang</th>
                                  <th class="text-center" style="width: 8%">Jumlah</th>
                                  <th class="text-end" style="width: 12%">Harga Beli</th>
                                  <th class="text-end" style="width: 15%">Subtotal</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="9" class="text-center text-muted py-4">
                                    <i class="mdi mdi-information-outline font-size-20"></i>
                                    <br>Silakan pilih filter dan klik tombol Tampilkan untuk melihat data
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>

                          <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                              <tbody>
                                <tr class="table-success fw-bold">
                                  <td class="text-end" colspan="8">Total Keseluruhan</td>
                                  <td class="text-end" id="total-pembelian">Rp 0,00</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Tab Stok -->
                    <div class="tab-pane" id="stok" role="tabpanel">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="card border">
                            <div class="card-header bg-light">
                              <h5 class="mb-0">Filter Laporan Stok Barang</h5>
                            </div>
                            <div class="card-body">
                              <form id="form-stok">
                                <div class="row">
                                  <div class="col-md-2">
                                    <label class="form-label">Jenis Stok</label>
                                    <select class="form-select" id="filter-jenis-stok">
                                      <option value="semua">Semua Barang</option>
                                      <!-- <option value="habis">Stok Habis</option>
                                      <option value="rendah">Stok Rendah</option>
                                      <option value="aman">Stok Aman</option> -->
                                    </select>
                                  </div>

                                  <div class="col-md-2">
                                    <label class="form-label">Batas Stok Rendah</label>
                                    <input type="number" class="form-control" id="batas-stok" value="5" min="1">
                                  </div>

                                  <div class="col-md-3">
                                    <label class="form-label">Nama Pimpinan</label>
                                    <input type="text" class="form-control" id="nama-pimpinan-stok" placeholder="Nama pimpinan untuk tanda tangan">
                                  </div>

                                  <div class="col-md-1 d-flex align-items-end">
                                    <div class="btn-group w-100">
                                      <button type="button" class="btn btn-primary" onclick="tampilkanDataStok()">
                                        <i class="mdi mdi-eye"></i> Tampilkan
                                      </button>
                                      <button type="button" class="btn btn-success" onclick="cetakLaporanStok()">
                                        <i class="mdi mdi-printer"></i> PDF
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row mt-3">
                        <div class="col-lg-12">
                          <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-stok">
                              <thead class="table-warning">
                                <tr>
                                  <th class="text-center" style="width: 5%">No</th>
                                  <th class="text-center" style="width: 12%">Kode Barang</th>
                                  <th style="width: 18%">Kategori</th>
                                  <th class="text-center" style="width: 10%">Satuan</th>
                                  <th class="text-center" style="width: 15%">Stok Tersedia</th>
                                  <th class="text-center" style="width: 15%">Status Barang</th>
                                  <th style="width: 25%">Nama Barang</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="7" class="text-center text-muted py-4">
                                    <i class="mdi mdi-information-outline font-size-20"></i>
                                    <br>Silakan pilih filter dan klik tombol Tampilkan untuk melihat data
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'template/script.php'; ?>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Filter penjualan
      document.getElementById('filter-jenis-penjualan').addEventListener('change', function() {
        resetFilterPenjualan();
        const value = this.value;

        document.getElementById('filter-harian-penjualan').classList.toggle('d-none', value !== 'harian');
        document.getElementById('filter-range-hari-penjualan').classList.toggle('d-none', value !== 'range-hari');
        document.getElementById('filter-range-hari-akhir-penjualan').classList.toggle('d-none', value !== 'range-hari');
        document.getElementById('filter-range-bulan-penjualan').classList.toggle('d-none', value !== 'range-bulan');
        document.getElementById('filter-range-bulan-akhir-penjualan').classList.toggle('d-none', value !== 'range-bulan');
        document.getElementById('filter-range-tahun-penjualan').classList.toggle('d-none', value !== 'range-tahun');
        document.getElementById('filter-range-tahun-akhir-penjualan').classList.toggle('d-none', value !== 'range-tahun');
      });

      // Filter pembelian
      document.getElementById('filter-jenis-pembelian').addEventListener('change', function() {
        resetFilterPembelian();
        const value = this.value;

        document.getElementById('filter-harian-pembelian').classList.toggle('d-none', value !== 'harian');
        document.getElementById('filter-range-hari-pembelian').classList.toggle('d-none', value !== 'range-hari');
        document.getElementById('filter-range-hari-akhir-pembelian').classList.toggle('d-none', value !== 'range-hari');
        document.getElementById('filter-range-bulan-pembelian').classList.toggle('d-none', value !== 'range-bulan');
        document.getElementById('filter-range-bulan-akhir-pembelian').classList.toggle('d-none', value !== 'range-bulan');
        document.getElementById('filter-range-tahun-pembelian').classList.toggle('d-none', value !== 'range-tahun');
        document.getElementById('filter-range-tahun-akhir-pembelian').classList.toggle('d-none', value !== 'range-tahun');
      });
    });

    function resetFilterPenjualan() {
      ['filter-harian-penjualan', 'filter-range-hari-penjualan', 'filter-range-hari-akhir-penjualan',
       'filter-range-bulan-penjualan', 'filter-range-bulan-akhir-penjualan',
       'filter-range-tahun-penjualan', 'filter-range-tahun-akhir-penjualan'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
      });
    }

    function resetFilterPembelian() {
      ['filter-harian-pembelian', 'filter-range-hari-pembelian', 'filter-range-hari-akhir-pembelian',
       'filter-range-bulan-pembelian', 'filter-range-bulan-akhir-pembelian',
       'filter-range-tahun-pembelian', 'filter-range-tahun-akhir-pembelian'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
      });
    }

    function formatRupiah(amount) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount);
    }

    function formatTanggalIndonesia(tanggal) {
      const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const date = new Date(tanggal);
      return date.getDate() + ' ' + bulan[date.getMonth()] + ' ' + date.getFullYear();
    }

    function tampilkanDataPenjualan() {
      const formData = new FormData();
      const jenisFilter = document.getElementById('filter-jenis-penjualan').value;

      formData.append('jenis_filter', jenisFilter);

      if (jenisFilter === 'semua') {
        // Tidak perlu parameter tambahan untuk "Semua"
      } else if (jenisFilter === 'harian') {
        const tanggal = document.getElementById('tanggal-penjualan').value;
        formData.append('tanggal_mulai', tanggal);
        formData.append('tanggal_akhir', tanggal);
      } else if (jenisFilter === 'range-hari') {
        const tanggalMulai = document.getElementById('tanggal-mulai-hari-penjualan').value;
        const tanggalAkhir = document.getElementById('tanggal-akhir-hari-penjualan').value;
        formData.append('tanggal_mulai', tanggalMulai);
        formData.append('tanggal_akhir', tanggalAkhir);
      } else if (jenisFilter === 'range-bulan') {
        const bulanMulai = document.getElementById('bulan-mulai-penjualan').value;
        const bulanAkhir = document.getElementById('bulan-akhir-penjualan').value;
        formData.append('bulan_mulai', bulanMulai);
        formData.append('bulan_akhir', bulanAkhir);
      } else if (jenisFilter === 'range-tahun') {
        const tahunMulai = document.getElementById('tahun-mulai-penjualan').value;
        const tahunAkhir = document.getElementById('tahun-akhir-penjualan').value;
        formData.append('tahun_mulai', tahunMulai);
        formData.append('tahun_akhir', tahunAkhir);
      }

      fetch('index.php?controller=laporan&action=getLaporanPenjualan', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          renderTablePenjualan(data.data);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengambil data', 'error');
      });
    }

    function renderTablePenjualan(data) {
      const tbody = document.querySelector('#table-penjualan tbody');
      const totalCell = document.getElementById('total-penjualan');

      let html = '';
      let total = 0;

      if (data.length > 0) {
        data.forEach((item, index) => {
          const subtotal = item.jumlah * item.harga_satuan;
          total += subtotal;

          html += `
            <tr>
              <td class="text-center">${index + 1}</td>
              <td class="text-center">${item.no_faktur}</td>
              <td class="text-center">${formatTanggalIndonesia(item.tanggal)}</td>
              <td class="text-center">${item.kode_barang}</td>
              <td>${item.nama_barang}</td>
              <td>${item.nama_pelanggan || 'Umum'}</td>
              <td class="text-center">${item.jumlah}</td>
              <td class="text-end">${formatRupiah(item.harga_satuan)}</td>
              <td class="text-end">${formatRupiah(subtotal)}</td>
            </tr>
          `;
        });
      } else {
        html = `
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              <i class="mdi mdi-information-outline font-size-20"></i>
              <br>Tidak ada data penjualan
            </td>
          </tr>
        `;
      }

      tbody.innerHTML = html;
      totalCell.textContent = formatRupiah(total);
    }

    function tampilkanDataPembelian() {
      const formData = new FormData();
      const jenisFilter = document.getElementById('filter-jenis-pembelian').value;

      formData.append('jenis_filter', jenisFilter);

      if (jenisFilter === 'semua') {
        // Tidak perlu parameter tambahan untuk "Semua"
      } else if (jenisFilter === 'harian') {
        const tanggal = document.getElementById('tanggal-pembelian').value;
        formData.append('tanggal_mulai', tanggal);
        formData.append('tanggal_akhir', tanggal);
      } else if (jenisFilter === 'range-hari') {
        const tanggalMulai = document.getElementById('tanggal-mulai-hari-pembelian').value;
        const tanggalAkhir = document.getElementById('tanggal-akhir-hari-pembelian').value;
        formData.append('tanggal_mulai', tanggalMulai);
        formData.append('tanggal_akhir', tanggalAkhir);
      } else if (jenisFilter === 'range-bulan') {
        const bulanMulai = document.getElementById('bulan-mulai-pembelian').value;
        const bulanAkhir = document.getElementById('bulan-akhir-pembelian').value;
        formData.append('bulan_mulai', bulanMulai);
        formData.append('bulan_akhir', bulanAkhir);
      } else if (jenisFilter === 'range-tahun') {
        const tahunMulai = document.getElementById('tahun-mulai-pembelian').value;
        const tahunAkhir = document.getElementById('tahun-akhir-pembelian').value;
        formData.append('tahun_mulai', tahunMulai);
        formData.append('tahun_akhir', tahunAkhir);
      }

      fetch('index.php?controller=laporan&action=getLaporanPembelian', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          renderTablePembelian(data.data);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengambil data', 'error');
      });
    }

    function renderTablePembelian(data) {
      const tbody = document.querySelector('#table-pembelian tbody');
      const totalCell = document.getElementById('total-pembelian');

      let html = '';
      let total = 0;

      if (data.length > 0) {
        data.forEach((item, index) => {
          const subtotal = item.jumlah * item.harga_beli;
          total += subtotal;

          html += `
            <tr>
              <td class="text-center">${index + 1}</td>
              <td class="text-center">${item.no_nota}</td>
              <td class="text-center">${formatTanggalIndonesia(item.tanggal)}</td>
              <td>${item.nama_supplier}</td>
              <td class="text-center">${item.kode_barang}</td>
              <td>${item.nama_barang}</td>
              <td class="text-center">${item.jumlah}</td>
              <td class="text-end">${formatRupiah(item.harga_beli)}</td>
              <td class="text-end">${formatRupiah(subtotal)}</td>
            </tr>
          `;
        });
      } else {
        html = `
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              <i class="mdi mdi-information-outline font-size-20"></i>
              <br>Tidak ada data pembelian
            </td>
          </tr>
        `;
      }

      tbody.innerHTML = html;
      totalCell.textContent = formatRupiah(total);
    }

    function tampilkanDataStok() {
      const formData = new FormData();
      const jenisStok = document.getElementById('filter-jenis-stok').value;
      const batasStok = document.getElementById('batas-stok').value;

      formData.append('jenis_stok', jenisStok);
      formData.append('batas_stok', batasStok);

      fetch('index.php?controller=laporan&action=getLaporanStokBarang', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          renderTableStok(data.data);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat mengambil data', 'error');
      });
    }

    function renderTableStok(data) {
      const tbody = document.querySelector('#table-stok tbody');
      const batasStok = document.getElementById('batas-stok').value;

      let html = '';

      if (data.length > 0) {
        data.forEach((item, index) => {
          let status = '';
          let statusClass = '';

          if (item.stok == 0) {
            status = 'HABIS';
            statusClass = 'bg-danger text-white fw-bold';
          } else if (item.stok <= parseInt(batasStok)) {
            status = 'RENDAH';
            statusClass = 'bg-warning text-dark fw-bold';
          } else {
            status = 'AMAN';
            statusClass = 'bg-success text-white fw-bold';
          }

          html += `
            <tr>
              <td class="text-center">${index + 1}</td>
              <td class="text-center">${item.kode_barang}</td>
              <td>${item.nama_kategori}</td>
              <td class="text-center">${item.satuan}</td>
              <td class="text-center">${item.stok}</td>
              <td class="text-center">
                <span class="badge ${statusClass}">${status}</span>
              </td>
              <td>${item.nama_barang}</td>
            </tr>
          `;
        });
      } else {
        html = `
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="mdi mdi-information-outline font-size-20"></i>
              <br>Tidak ada data barang
            </td>
          </tr>
        `;
      }

      tbody.innerHTML = html;
    }

    function cetakLaporanPenjualan() {
      const jenisFilter = document.getElementById('filter-jenis-penjualan').value;
      const namaPimpinan = document.getElementById('nama-pimpinan-penjualan').value;
      let url = 'index.php?controller=laporan&action=cetakPdfPenjualan';

      url += `&jenis_filter=${jenisFilter}`;

      if (jenisFilter === 'semua') {
        // Tidak perlu parameter tambahan untuk "Semua"
      } else if (jenisFilter === 'harian') {
        const tanggal = document.getElementById('tanggal-penjualan').value;
        url += `&tanggal_mulai=${tanggal}&tanggal_akhir=${tanggal}`;
      } else if (jenisFilter === 'range-hari') {
        const tanggalMulai = document.getElementById('tanggal-mulai-hari-penjualan').value;
        const tanggalAkhir = document.getElementById('tanggal-akhir-hari-penjualan').value;
        url += `&tanggal_mulai=${tanggalMulai}&tanggal_akhir=${tanggalAkhir}`;
      } else if (jenisFilter === 'range-bulan') {
        const bulanMulai = document.getElementById('bulan-mulai-penjualan').value;
        const bulanAkhir = document.getElementById('bulan-akhir-penjualan').value;
        url += `&bulan_mulai=${bulanMulai}&bulan_akhir=${bulanAkhir}`;
      } else if (jenisFilter === 'range-tahun') {
        const tahunMulai = document.getElementById('tahun-mulai-penjualan').value;
        const tahunAkhir = document.getElementById('tahun-akhir-penjualan').value;
        url += `&tahun_mulai=${tahunMulai}&tahun_akhir=${tahunAkhir}`;
      }

      if (namaPimpinan) {
        url += `&nama_pimpinan=${encodeURIComponent(namaPimpinan)}`;
      }

      window.open(url, '_blank');
    }

    function cetakLaporanPembelian() {
      const jenisFilter = document.getElementById('filter-jenis-pembelian').value;
      const namaPimpinan = document.getElementById('nama-pimpinan-pembelian').value;
      let url = 'index.php?controller=laporan&action=cetakPdfPembelian';

      url += `&jenis_filter=${jenisFilter}`;

      if (jenisFilter === 'semua') {
        // Tidak perlu parameter tambahan untuk "Semua"
      } else if (jenisFilter === 'harian') {
        const tanggal = document.getElementById('tanggal-pembelian').value;
        url += `&tanggal_mulai=${tanggal}&tanggal_akhir=${tanggal}`;
      } else if (jenisFilter === 'range-hari') {
        const tanggalMulai = document.getElementById('tanggal-mulai-hari-pembelian').value;
        const tanggalAkhir = document.getElementById('tanggal-akhir-hari-pembelian').value;
        url += `&tanggal_mulai=${tanggalMulai}&tanggal_akhir=${tanggalAkhir}`;
      } else if (jenisFilter === 'range-bulan') {
        const bulanMulai = document.getElementById('bulan-mulai-pembelian').value;
        const bulanAkhir = document.getElementById('bulan-akhir-pembelian').value;
        url += `&bulan_mulai=${bulanMulai}&bulan_akhir=${bulanAkhir}`;
      } else if (jenisFilter === 'range-tahun') {
        const tahunMulai = document.getElementById('tahun-mulai-pembelian').value;
        const tahunAkhir = document.getElementById('tahun-akhir-pembelian').value;
        url += `&tahun_mulai=${tahunMulai}&tahun_akhir=${tahunAkhir}`;
      }

      if (namaPimpinan) {
        url += `&nama_pimpinan=${encodeURIComponent(namaPimpinan)}`;
      }

      window.open(url, '_blank');
    }

    function cetakLaporanStok() {
      const jenisStok = document.getElementById('filter-jenis-stok').value;
      const batasStok = document.getElementById('batas-stok').value;
      const namaPimpinan = document.getElementById('nama-pimpinan-stok').value;

      let url = `index.php?controller=laporan&action=cetakPdfStokBarang&jenis_stok=${jenisStok}&batas_stok=${batasStok}`;

      if (namaPimpinan) {
        url += `&nama_pimpinan=${encodeURIComponent(namaPimpinan)}`;
      }

      window.open(url, '_blank');
    }

      </script>
</body>
</html>