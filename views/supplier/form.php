<?php 
include('template/header.php'); 

// Cek apakah ini mode edit
$mode_edit = isset($_GET['id']) && !empty($_GET['id']);
$supplier = null;

if ($mode_edit) {
    require_once 'models/SupplierModel.php';
    $model = new SupplierModel();
    $supplier = $model->getById($_GET['id']);
    
    if (!$supplier) {
        echo "<script>alert('Supplier tidak ditemukan!'); window.location.href='index.php?controller=supplier&action=index';</script>";
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
                    <?php echo $mode_edit ? 'Edit Supplier' : 'Tambah Supplier'; ?>
                  </h2>
                </div>
                <a href="index.php?controller=supplier&action=index" class="btn btn-light btn-icon-text">
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
                    <?php echo $mode_edit ? 'Edit Supplier' : 'Tambah Supplier Baru'; ?>
                  </h4>
                  
                  <form method="POST" action="index.php?controller=supplier&action=<?php echo $mode_edit ? 'edit&id='.$_GET['id'] : 'tambah'; ?>">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label for="nama_supplier">Nama Supplier <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="nama_supplier" 
                                   name="nama_supplier" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($supplier['nama_supplier']) : ''; ?>" 
                                   placeholder="Masukkan nama supplier" 
                                   required
                                   maxlength="150">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="alamat">Alamat</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-map-marker"></i></span>
                            </div>
                            <textarea 
                                   class="form-control" 
                                   style="height: 80px;"
                                   id="alamat" 
                                   name="alamat" 
                                   placeholder="Masukkan alamat supplier"><?php echo $mode_edit ? htmlspecialchars($supplier['alamat']) : ''; ?></textarea>
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="no_hp">No. HP <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-phone"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="no_hp" 
                                   name="no_hp" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($supplier['no_hp']) : ''; ?>" 
                                   placeholder="Masukkan nomor HP supplier" 
                                   required
                                   maxlength="20">
                          </div>
                        </div>
                        
                        <div class="form-group mt-4">
                          <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                              <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
                            </button>
                            <a href="index.php?controller=supplier&action=index" class="btn btn-light btn-icon-text ml-2">
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
          const nama_supplier = document.getElementById('nama_supplier').value.trim();
          const no_hp = document.getElementById('no_hp').value.trim();
          
          if (nama_supplier === '') {
            e.preventDefault();
            alert('Nama supplier tidak boleh kosong!');
            document.getElementById('nama_supplier').focus();
            return false;
          }
          
          if (no_hp === '') {
            e.preventDefault();
            alert('Nomor HP tidak boleh kosong!');
            document.getElementById('no_hp').focus();
            return false;
          }
          
          // Validasi format nomor HP
          const phoneRegex = /^[0-9+\-\s()]+$/;
          if (!phoneRegex.test(no_hp)) {
            e.preventDefault();
            alert('Format nomor HP tidak valid!');
            document.getElementById('no_hp').focus();
            return false;
          }
        });
      }
    });
  </script>
</body>
</html>