<?php include('template/header.php'); 

require_once 'models/PembelianBarangModel.php';
$model = new PembelianBarangModel();

$id = $_GET['id'] ?? 0;
$pembelian = $model->getById($id);
$detail_pembelian = $model->getDetailByPembelianId($id);

if (!$pembelian) {
    echo "<script>alert('Pembelian tidak ditemukan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
    exit();
}
?>

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
                  <h2 class="mb-1">Detail Pembelian Barang</h2>
                </div>
                <a href="index.php?controller=pembelianBarang&action=index" class="btn btn-light btn-icon-text">
                  <i class="mdi mdi-arrow-left btn-icon-prepend"></i> Kembali
                </a>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-4">Informasi Pembelian</h4>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>No. Nota:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-receipt"></i></span>
                          </div>
                          <input type="text" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo htmlspecialchars($pembelian['no_nota']); ?>" 
                                 readonly>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label>Supplier:</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account-multiple"></i></span>
                          </div>
                          <input type="text" 
                                 class="form-control" 
                                 style="height: 40px;"
                                 value="<?php echo htmlspecialchars($pembelian['nama_supplier']); ?>" 
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
                                 value="<?php echo $pembelian['total']; ?>" 
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
                                 value="<?php echo date('d-m-Y', strtotime($pembelian['tanggal'])) . ' - ' . htmlspecialchars($pembelian['nama_user']); ?>" 
                                 readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mt-4">
                    <div class="col-12">
                      <h5>Detail Barang Dibeli</h5>
                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead class="thead-light">
                            <tr>
                              <th width="5%">No</th>
                              <th>Nama Barang</th>
                              <th>Kode Barang</th>
                              <th>Satuan</th>
                              <th>Jumlah</th>
                              <th>Harga Beli</th>
                              <th>Subtotal</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($detail_pembelian)): ?>
                              <?php $no = 1; foreach ($detail_pembelian as $item): ?>
                                <tr>
                                  <td><?php echo $no++; ?></td>
                                  <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                  <td><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                                  <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                                  <td><?php echo $item['jumlah']; ?></td>
                                  <td>Rp <?php echo number_format($item['harga_beli'], 0, ',', '.'); ?></td>
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
                              <td><strong>Rp <?php echo number_format($pembelian['total'], 0, ',', '.'); ?></strong></td>
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
                          <a href="index.php?controller=pembelianBarang&action=edit&id=<?php echo $pembelian['id_pembelian']; ?>" class="btn btn-warning btn-icon-text mr-2">
                            <i class="mdi mdi-file-edit btn-icon-prepend"></i> Edit
                          </a>
                        <?php endif; ?>
                        <a href="index.php?controller=pembelianBarang&action=index" class="btn btn-light btn-icon-text">
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