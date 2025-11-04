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
                  <h2 class="mb-1">Manajemen User</h2>
                </div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <a href="index.php?controller=user&action=tambah" class="btn btn-primary btn-icon-text">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah User
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
                    <h4 class="card-title mb-0">Daftar User</h4>
                    <div class="d-flex">
                      <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Cari user...">
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
                          <th>Username</th>
                          <th>Nama Lengkap</th>
                          <th>Role</th>
                          <th>Dibuat Pada</th>
                          <th width="20%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($users)): ?>
                          <?php $no = 1; foreach ($users as $item): ?>
                            <tr>
                              <td><?php echo $no++; ?></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="mr-2">
                                    <div class="icon-rounded bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                                      <i class="mdi mdi-account"></i>
                                    </div>
                                  </div>
                                  <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['username']); ?></h6>
                                  </div>
                                </div>
                              </td>
                              <td><?php echo htmlspecialchars($item['nama_lengkap']); ?></td>
                              <td>
                                <?php 
                                  $role_badge = '';
                                  switch($item['role']) {
                                      case 'admin': 
                                          $role_badge = 'badge-danger'; 
                                          break;
                                      case 'kasir': 
                                          $role_badge = 'badge-warning'; 
                                          break;
                                      case 'owner': 
                                          $role_badge = 'badge-success'; 
                                          break;
                                      default: 
                                          $role_badge = 'badge-secondary'; 
                                          break;
                                  }
                                ?>
                                <span class="badge <?php echo $role_badge; ?>">
                                  <?php echo ucfirst($item['role']); ?>
                                </span>
                              </td>
                              <td><?php echo date('d-m-Y H:i', strtotime($item['created_at'])); ?></td>
                              <td>
                                <div class="btn-group" role="group">
                                  <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="index.php?controller=user&action=edit&id=<?php echo $item['id_user']; ?>" 
                                       class="btn btn-sm btn-outline-warning btn-icon-text" title="Edit">
                                      <i class="mdi mdi-file-edit btn-icon-prepend"></i> Edit
                                    </a>
                                  <?php endif; ?>
                                  
                                  <?php if ($_SESSION['role'] === 'admin' && $item['id_user'] != $_SESSION['user_id']): ?>
                                    <a href="index.php?controller=user&action=hapus&id=<?php echo $item['id_user']; ?>" 
                                       class="btn btn-sm btn-outline-danger btn-icon-text" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"
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
                            <td colspan="6" class="text-center py-5">
                              <div class="empty-state">
                                <i class="mdi mdi-account-multiple mdi-48px text-muted"></i>
                                <h5 class="mt-3">Tidak ada data user</h5>
                                <p>Belum ada user yang dibuat</p>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                  <a href="index.php?controller=user&action=tambah" class="btn btn-primary mt-2">Buat User Pertama</a>
                                <?php endif; ?>
                              </div>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  
                  <?php if (!empty($users)): ?>
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
      const searchInput = document.querySelector('input[placeholder="Cari user..."]');
      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          const searchTerm = this.value.toLowerCase();
          const tableRows = document.querySelectorAll('tbody tr');
          
          tableRows.forEach(function(row) {
            const username = row.querySelector('h6').textContent.toLowerCase();
            const namaLengkap = row.cells[2].textContent.toLowerCase(); // Nama Lengkap column
            if (username.includes(searchTerm) || namaLengkap.includes(searchTerm)) {
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