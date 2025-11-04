<?php include('template/header.php'); ?>

<body>
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              <h2 class="mb-4">Dashboard</h2>
            </div>
          </div>
          
          <!-- Stats Cards -->
          <div class="row">
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-package text-success mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($total_barang, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Total Barang</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-inventory text-info mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($total_stok, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Total Stok</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-cash-multiple text-warning mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Total Pendapatan</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-chart-line text-danger mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($total_penjualan, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Total Penjualan</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Stats Cards 2 -->
          <div class="row">
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-alert-circle text-danger mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($barang_habis, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Barang Habis</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-alert text-warning mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($barang_stok_rendah, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Stok Rendah</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-calendar-check text-primary mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0"><?php echo number_format($penjualan_hari_ini, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Penjualan Hari Ini</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center justify-content-sm-start">
                        <i class="mdi mdi-cash-usd text-success mr-2" style="font-size: 2rem;"></i>
                        <div>
                          <h4 class="mb-0">Rp <?php echo number_format($pendapatan_hari_ini, 0, ',', '.'); ?></h4>
                          <p class="mb-0">Pendapatan Hari Ini</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <!-- Barang Terlaris -->
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Barang Terlaris</h4>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Nama Barang</th>
                          <th>Jumlah Terjual</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($barang_terlaris)): ?>
                          <?php foreach ($barang_terlaris as $barang): ?>
                            <tr>
                              <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                              <td><?php echo number_format($barang['jumlah_terjual'], 0, ',', '.'); ?> pcs</td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="2" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Transaksi Terakhir -->
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Transaksi Terakhir</h4>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>No. Faktur</th>
                          <th>Tanggal</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($transaksi_terakhir)): ?>
                          <?php foreach ($transaksi_terakhir as $transaksi): ?>
                            <tr>
                              <td><?php echo htmlspecialchars($transaksi['no_faktur']); ?></td>
                              <td><?php echo date('d-m-Y', strtotime($transaksi['tanggal'])); ?></td>
                              <td>Rp <?php echo number_format($transaksi['total'], 0, ',', '.'); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php endif; ?>
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
  <?php include 'template/script.php'; ?>
</body>
</html>