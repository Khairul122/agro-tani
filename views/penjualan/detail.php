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
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="mb-1">Detail Penjualan Barang</h2>
                </div>
                <a href="index.php?controller=penjualanBarang&action=index" class="btn btn-light btn-icon-text">
                  <i class="mdi mdi-arrow-left btn-icon-prepend"></i> Kembali
                </a>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-4">Informasi Penjualan</h4>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>No. Faktur:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-receipt"></i></span>
                          </div>
                          <input type="text" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo htmlspecialchars($penjualan['no_faktur']); ?>" 
                                 readonly>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label>Pelanggan:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account-multiple"></i></span>
                          </div>
                          <input type="text" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo htmlspecialchars($penjualan['nama_pelanggan']); ?>" 
                                 readonly>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Total:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;">Rp</span>
                          </div>
                          <input type="number" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo $penjualan['total']; ?>" 
                                 readonly>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label>Tanggal & User:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-calendar"></i></span>
                          </div>
                          <input type="text" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo date('d-m-Y', strtotime($penjualan['tanggal'])) . ' - ' . htmlspecialchars($penjualan['nama_user']); ?>" 
                                 readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mt-4">
                    <div class="col-12">
                      <h5>Detail Barang Dijual</h5>
                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead class="thead-light">
                            <tr>
                              <th width="5%">No</th>
                              <th>Nama Barang</th>
                              <th>Kode Barang</th>
                              <th>Satuan</th>
                              <th>Jumlah</th>
                              <th>Harga Satuan</th>
                              <th>Subtotal</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($detail_penjualan)): ?>
                              <?php $no = 1; foreach ($detail_penjualan as $item): ?>
                                <tr>
                                  <td><?php echo $no++; ?></td>
                                  <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                  <td><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                                  <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                                  <td><?php echo $item['jumlah']; ?></td>
                                  <td>Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                                  <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="7" class="text-center py-3">Tidak ada detail barang</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="6" class="text-right"><strong>Total Keseluruhan:</strong></td>
                              <td><strong>Rp <?php echo number_format($penjualan['total'], 0, ',', '.'); ?></strong></td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mt-4">
                    <div class="col-12">
                      <div class="d-flex">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                          <a href="index.php?controller=penjualanBarang&action=edit&id=<?php echo $penjualan['id_penjualan']; ?>" class="btn btn-warning btn-icon-text mr-2">
                            <i class="mdi mdi-file-edit btn-icon-prepend"></i> Edit
                          </a>
                        <?php endif; ?>
                        <a href="index.php?controller=penjualanBarang&action=index" class="btn btn-light btn-icon-text">
                          <i class="mdi mdi-close btn-icon-prepend"></i> Kembali
                        </a>
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
</body>
</html>