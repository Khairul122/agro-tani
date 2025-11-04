<?php 
include('template/header.php'); 

// Cek apakah ini mode edit
$mode_edit = isset($_GET['id']) && !empty($_GET['id']);
$kategori = null;

if ($mode_edit) {
    require_once 'models/KategoriBarangModel.php';
    $model = new KategoriBarangModel();
    $kategori = $model->getById($_GET['id']);
    if (!$kategori) {
        echo "<script>alert('Kategori tidak ditemukan!'); window.location.href='index.php?controller=kategoriBarang&action=index';</script>";
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
                    <?php echo $mode_edit ? 'Edit Kategori Barang' : 'Tambah Kategori Barang'; ?>
                  </h2>
                </div>
                <a href="index.php?controller=kategoriBarang&action=index" class="btn btn-light btn-icon-text">
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
                    <?php echo $mode_edit ? 'Edit Kategori Barang' : 'Tambah Kategori Barang'; ?>
                  </h4>
                  
                  <form method="POST" action="index.php?controller=kategoriBarang&action=<?php echo $mode_edit ? 'edit&id='.$_GET['id'] : 'tambah'; ?>" style="min-height: 400px;">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-package-variant"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="nama_kategori" 
                                   name="nama_kategori" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($kategori['nama_kategori']) : ''; ?>" 
                                   placeholder="Masukkan nama kategori" 
                                   required
                                   maxlength="100">
                          </div>
                          <small class="form-text text-muted">* Wajib diisi (maksimal 100 karakter)</small>
                        </div>
                        
                        <div class="form-group mt-4">
                          <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                              <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
                            </button>
                            <a href="index.php?controller=kategoriBarang&action=index" class="btn btn-light btn-icon-text ml-2">
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
          const nama_kategori = document.getElementById('nama_kategori').value.trim();
          
          if (nama_kategori === '') {
            e.preventDefault();
            alert('Nama kategori tidak boleh kosong!');
            document.getElementById('nama_kategori').focus();
            return false;
          }
          
          if (nama_kategori.length > 100) {
            e.preventDefault();
            alert('Nama kategori terlalu panjang! Maksimal 100 karakter.');
            return false;
          }
        });
      }
    });
  </script>
</body>
</html>