<?php 
include('template/header.php'); 

// Cek apakah ini mode edit
$mode_edit = isset($_GET['id']) && !empty($_GET['id']);
$user = null;

if ($mode_edit) {
    require_once 'models/UserModel.php';
    $model = new UserModel();
    $user = $model->getById($_GET['id']);
    
    if (!$user) {
        echo "<script>alert('User tidak ditemukan!'); window.location.href='index.php?controller=user&action=index';</script>";
        exit();
    }
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
                  <h2 class="mb-1">
                    <?php echo $mode_edit ? 'Edit User' : 'Tambah User'; ?>
                  </h2>
                </div>
                <a href="index.php?controller=user&action=index" class="btn btn-light btn-icon-text">
                  <i class="mdi mdi-arrow-left btn-icon-prepend"></i> Kembali
                </a>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title mb-4">
                    <?php echo $mode_edit ? 'Edit User' : 'Tambah User Baru'; ?>
                  </h4>
                  
                  <form method="POST" action="index.php?controller=user&action=<?php echo $mode_edit ? 'edit&id='.$_GET['id'] : 'tambah'; ?>">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label for="username">Username <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="username" 
                                   name="username" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($user['username']) : ''; ?>" 
                                   placeholder="Masukkan username" 
                                   required
                                   maxlength="50">
                          </div>
                        </div>
                        
                        <?php if (!$mode_edit): ?>
                        <div class="form-group">
                          <label for="password">Password <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-lock"></i></span>
                            </div>
                            <input type="password" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password" 
                                   required
                                   minlength="6">
                          </div>
                        </div>
                        <?php else: ?>
                        <div class="form-group">
                          <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-lock"></i></span>
                            </div>
                            <input type="password" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="password" 
                                   name="password" 
                                   placeholder="Kosongkan jika tidak ingin diubah"
                                   minlength="6">
                          </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                          <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account-circle"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="nama_lengkap" 
                                   name="nama_lengkap" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($user['nama_lengkap']) : ''; ?>" 
                                   placeholder="Masukkan nama lengkap" 
                                   required
                                   maxlength="100">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="role">Role <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-shield-account"></i></span>
                            </div>
                            <select class="form-control" 
                                    style="height: 40px;"
                                    id="role" 
                                    name="role" 
                                    required>
                              <option value="">Pilih role</option>
                              <option value="admin" <?php echo ($mode_edit && $user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                              <option value="kasir" <?php echo ($mode_edit && $user['role'] == 'kasir') ? 'selected' : ''; ?>>Kasir</option>
                              <option value="owner" <?php echo ($mode_edit && $user['role'] == 'owner') ? 'selected' : ''; ?>>Owner</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="form-group mt-4">
                          <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                              <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
                            </button>
                            <a href="index.php?controller=user&action=index" class="btn btn-light btn-icon-text ml-2">
                              <i class="mdi mdi-close btn-icon-prepend"></i> Batal
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
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
    // Validasi form sebelum submit
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      if (form) {
        form.addEventListener('submit', function(e) {
          const username = document.getElementById('username').value.trim();
          const nama_lengkap = document.getElementById('nama_lengkap').value.trim();
          const role = document.getElementById('role').value;
          let isValid = true;
          
          if (username === '') {
            e.preventDefault();
            alert('Username tidak boleh kosong!');
            document.getElementById('username').focus();
            isValid = false;
          }
          
          if (nama_lengkap === '') {
            if (isValid) {
              e.preventDefault();
              alert('Nama lengkap tidak boleh kosong!');
              document.getElementById('nama_lengkap').focus();
              isValid = false;
            }
          }
          
          if (role === '') {
            if (isValid) {
              e.preventDefault();
              alert('Role harus dipilih!');
              document.getElementById('role').focus();
              isValid = false;
            }
          }
          
          // Validasi password untuk mode tambah
          <?php if (!$mode_edit): ?>
          const password = document.getElementById('password').value.trim();
          if (password === '') {
            if (isValid) {
              e.preventDefault();
              alert('Password tidak boleh kosong!');
              document.getElementById('password').focus();
              isValid = false;
            }
          } else if (password.length < 6) {
            if (isValid) {
              e.preventDefault();
              alert('Password minimal 6 karakter!');
              document.getElementById('password').focus();
              isValid = false;
            }
          }
          <?php endif; ?>
          
          // Validasi password untuk mode edit (hanya jika diisi)
          <?php if ($mode_edit): ?>
          const password = document.getElementById('password').value.trim();
          if (password !== '' && password.length < 6) {
            if (isValid) {
              e.preventDefault();
              alert('Jika ingin mengganti password, minimal 6 karakter!');
              document.getElementById('password').focus();
              isValid = false;
            }
          }
          <?php endif; ?>
        });
      }
    });
  </script>
</body>
</html>