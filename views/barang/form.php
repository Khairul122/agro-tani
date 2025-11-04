<?php 
include('template/header.php'); 

// Cek apakah ini mode edit
$mode_edit = isset($_GET['id']) && !empty($_GET['id']);
$barang = null;
$kategori = [];

if ($mode_edit) {
    require_once 'models/BarangModel.php';
    $model = new BarangModel();
    $barang = $model->getById($_GET['id']);
    $kategori = $model->getKategoriAll();
    
    if (!$barang) {
        echo "<script>alert('Barang tidak ditemukan!'); window.location.href='index.php?controller=barang&action=index';</script>";
        exit();
    }
} else {
    // Jika mode tambah, ambil semua kategori
    require_once 'models/BarangModel.php';
    $model = new BarangModel();
    $kategori = $model->getKategoriAll();
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
                    <?php echo $mode_edit ? 'Edit Barang' : 'Tambah Barang'; ?>
                  </h2>
                </div>
                <a href="index.php?controller=barang&action=index" class="btn btn-light btn-icon-text">
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
                    <?php echo $mode_edit ? 'Edit Barang' : 'Tambah Barang Baru'; ?>
                  </h4>
                  
                  <form method="POST" action="index.php?controller=barang&action=<?php echo $mode_edit ? 'edit&id='.$_GET['id'] : 'tambah'; ?>">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label for="kode_barang">Kode Barang <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-barcode"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="kode_barang" 
                                   name="kode_barang" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($barang['kode_barang']) : ''; ?>" 
                                   placeholder="Masukkan kode barang" 
                                   required
                                   maxlength="30">
                          </div>

                        </div>
                        
                        <div class="form-group">
                          <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-package-variant"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="nama_barang" 
                                   name="nama_barang" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($barang['nama_barang']) : ''; ?>" 
                                   placeholder="Masukkan nama barang" 
                                   required
                                   maxlength="150">
                          </div>

                        </div>
                        
                        <div class="form-group">
                          <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-folder"></i></span>
                            </div>
                            <select class="form-control" 
                                    style="height: 40px;"
                                    id="id_kategori" 
                                    name="id_kategori" 
                                    required>
                              <option value="">Pilih kategori</option>
                              <?php foreach ($kategori as $kat): ?>
                                <option value="<?php echo $kat['id_kategori']; ?>" 
                                        <?php echo ($mode_edit && $barang['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                  <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                        </div>
                        
                        <div class="form-group">
                          <label for="satuan">Satuan <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-scale"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="satuan" 
                                   name="satuan" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($barang['satuan']) : ''; ?>" 
                                   placeholder="Contoh: kg, pcs, liter" 
                                   required
                                   maxlength="50">
                          </div>

                        </div>
                        
                        <div class="form-group">
                          <label for="harga_beli">Harga Beli <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;">Rp</span>
                            </div>
                            <input type="number" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="harga_beli" 
                                   name="harga_beli" 
                                   value="<?php echo $mode_edit ? $barang['harga_beli'] : ''; ?>" 
                                   placeholder="Masukkan harga beli" 
                                   required
                                   min="0"
                                   step="100">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="harga_jual">Harga Jual <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;">Rp</span>
                            </div>
                            <input type="number" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="harga_jual" 
                                   name="harga_jual" 
                                   value="<?php echo $mode_edit ? $barang['harga_jual'] : ''; ?>" 
                                   placeholder="Masukkan harga jual" 
                                   required
                                   min="0"
                                   step="100">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="stok">Stok <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-package-variant-closed"></i></span>
                            </div>
                            <input type="number" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="stok" 
                                   name="stok" 
                                   value="<?php echo $mode_edit ? $barang['stok'] : '0'; ?>" 
                                   placeholder="Masukkan jumlah stok" 
                                   required
                                   min="0"
                                   step="1">
                          </div>
                        </div>
                        
                        <div class="form-group mt-4">
                          <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                              <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
                            </button>
                            <a href="index.php?controller=barang&action=index" class="btn btn-light btn-icon-text ml-2">
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
          const kode_barang = document.getElementById('kode_barang').value.trim();
          const nama_barang = document.getElementById('nama_barang').value.trim();
          const id_kategori = document.getElementById('id_kategori').value;
          const satuan = document.getElementById('satuan').value.trim();
          const harga_beli = document.getElementById('harga_beli').value.trim();
          const harga_jual = document.getElementById('harga_jual').value.trim();
          
          if (kode_barang === '') {
            e.preventDefault();
            alert('Kode barang tidak boleh kosong!');
            document.getElementById('kode_barang').focus();
            return false;
          }
          
          if (nama_barang === '') {
            e.preventDefault();
            alert('Nama barang tidak boleh kosong!');
            document.getElementById('nama_barang').focus();
            return false;
          }
          
          if (id_kategori === '') {
            e.preventDefault();
            alert('Kategori harus dipilih!');
            document.getElementById('id_kategori').focus();
            return false;
          }
          
          if (satuan === '') {
            e.preventDefault();
            alert('Satuan tidak boleh kosong!');
            document.getElementById('satuan').focus();
            return false;
          }
          
          if (harga_beli === '' || parseFloat(harga_beli) < 0) {
            e.preventDefault();
            alert('Harga beli harus berupa angka positif!');
            document.getElementById('harga_beli').focus();
            return false;
          }
          
          if (harga_jual === '' || parseFloat(harga_jual) < 0) {
            e.preventDefault();
            alert('Harga jual harus berupa angka positif!');
            document.getElementById('harga_jual').focus();
            return false;
          }
          
          if (parseFloat(harga_jual) < parseFloat(harga_beli)) {
            e.preventDefault();
            alert('Harga jual tidak boleh lebih rendah dari harga beli!');
            document.getElementById('harga_jual').focus();
            return false;
          }
        });
      }
    });
  </script>
</body>
</html>