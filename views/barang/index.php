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
                  <h2 class="mb-1">Manajemen Barang</h2>
                </div>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'kasir'): ?>
                  <a href="index.php?controller=barang&action=tambah" class="btn btn-primary btn-icon-text">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Barang
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Barang</h4>
                    <div class="d-flex">
                      <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Cari barang...">
                        <div class="input-group-append">
                          <span class="input-group-text bg-primary text-white"><i class="mdi mdi-magnify"></i></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="table-responsive mt-3">
                    <table class="table table-hover table-striped">
                      <thead class="thead-light">
                        <tr>
                          <th width="5%">No</th>
                          <th>Kode Barang</th>
                          <th>Nama Barang</th>
                          <th>Kategori</th>
                          <th>Satuan</th>
                          <th>Harga Jual</th>
                          <th>Stok</th>
                          <th width="20%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($barang)): ?>
                          <?php $no = 1; foreach ($barang as $item): ?>
                            <tr>
                              <td><?php echo $no++; ?></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="mr-2">
                                    <div class="icon-rounded bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                                      <i class="mdi mdi-barcode"></i>
                                    </div>
                                  </div>
                                  <div>
                                    <h6 class="mb-0 text-primary"><?php echo htmlspecialchars($item['kode_barang']); ?></h6>
                                  </div>
                                </div>
                              </td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['nama_barang']); ?></h6>
                                  </div>
                                </div>
                              </td>
                              <td>
                                <span class="badge badge-warning"><?php echo htmlspecialchars($item['nama_kategori']); ?></span>
                              </td>
                              <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                              <td>Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                              <td>
                                <?php 
                                  $stok_class = '';
                                  if ($item['stok'] == 0) {
                                    $stok_class = 'text-danger font-weight-bold';
                                  } elseif ($item['stok'] < 5) {
                                    $stok_class = 'text-warning font-weight-bold';
                                  } else {
                                    $stok_class = 'text-success';
                                  }
                                ?>
                                <span class="<?php echo $stok_class; ?>"><?php echo $item['stok']; ?> <?php echo htmlspecialchars($item['satuan']); ?></span>

                              </td>
                              <td>
                                <div class="btn-group" role="group">
                                  <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'kasir'): ?>
                                    <a href="index.php?controller=barang&action=edit&id=<?php echo $item['id_barang']; ?>" 
                                       class="btn btn-sm btn-outline-warning btn-icon-text" title="Edit">
                                      <i class="mdi mdi-file-edit btn-icon-prepend"></i> Edit
                                    </a>
                                  <?php endif; ?>
                                  
                                  <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="index.php?controller=barang&action=hapus&id=<?php echo $item['id_barang']; ?>" 
                                       class="btn btn-sm btn-outline-danger btn-icon-text" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')"
                                       title="Hapus">
                                      <i class="mdi mdi-delete btn-icon-prepend"></i> Hapus
                                    </a>
                                  <?php endif; ?>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="8" class="text-center py-5">
                              <div class="empty-state">
                                <i class="mdi mdi-package-variant-closed mdi-48px"></i>
                                <h5 class="mt-3">Tidak ada data barang</h5>
                                <p>Belum ada barang yang dibuat</p>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'kasir'): ?>
                                  <a href="index.php?controller=barang&action=tambah" class="btn btn-primary mt-2">Buat Barang Pertama</a>
                                <?php endif; ?>
                              </div>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  
                  <?php if (!empty($barang)): ?>
                    <nav aria-label="Page navigation">
                      <ul class="pagination justify-content-end mt-3">
                        <li class="page-item disabled">
                          <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                          <a class="page-link" href="#">Next</a>
                        </li>
                      </ul>
                    </nav>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>
  
  <script>
    // Fungsi untuk pencarian
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.querySelector('input[placeholder="Cari barang..."]');
      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          const searchTerm = this.value.toLowerCase();
          const tableRows = document.querySelectorAll('tbody tr');
          
          tableRows.forEach(function(row) {
            const kodeBarang = row.querySelector('.text-primary').textContent.toLowerCase();
            const namaBarang = row.querySelector('h6').textContent.toLowerCase();
            if (kodeBarang.includes(searchTerm) || namaBarang.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    });
  </script>
</body>
</html>