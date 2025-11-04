<?php 
include('template/header.php'); 

// Cek apakah ini mode edit
$mode_edit = isset($_GET['id']) && !empty($_GET['id']);
$pembelian = null;
$detail_pembelian = [];

if ($mode_edit) {
    require_once 'models/PembelianBarangModel.php';
    $model = new PembelianBarangModel();
    $pembelian = $model->getById($_GET['id']);
    $detail_pembelian = $model->getDetailByPembelianId($_GET['id']);
    
    if (!$pembelian) {
        echo "<script>alert('Pembelian tidak ditemukan!'); window.location.href='index.php?controller=pembelianBarang&action=index';</script>";
        exit();
    }
} else {
    // Jika mode tambah, inisialisasi model
    require_once 'models/PembelianBarangModel.php';
    $model = new PembelianBarangModel();
}

$supplier = $model->getSupplierAll();
$barang = $model->getBarangAll();
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
                    <?php echo $mode_edit ? 'Edit Pembelian Barang' : 'Tambah Pembelian Barang'; ?>
                  </h2>
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
                  <h4 class="card-title mb-4">
                    <?php echo $mode_edit ? 'Edit Pembelian Barang' : 'Tambah Pembelian Barang Baru'; ?>
                  </h4>
                  
                  <form method="POST" action="index.php?controller=pembelianBarang&action=<?php echo $mode_edit ? 'edit&id='.$_GET['id'] : 'tambah'; ?>">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="no_nota">No. Nota <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-receipt"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="no_nota" 
                                   name="no_nota" 
                                   value="<?php echo $mode_edit ? htmlspecialchars($pembelian['no_nota']) : $model->generateNoNotaOtomatis(); ?>" 
                                   placeholder="No. nota akan digenerate otomatis" 
                                   <?php echo $mode_edit ? 'required' : 'readonly'; ?>
                                   maxlength="50">
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-account-multiple"></i></span>
                            </div>
                            <select class="form-control" 
                                    style="height: 40px;"
                                    id="id_supplier" 
                                    name="id_supplier" 
                                    required>
                              <option value="">Pilih supplier</option>
                              <?php foreach ($supplier as $sup): ?>
                                <option value="<?php echo $sup['id_supplier']; ?>" 
                                        <?php echo ($mode_edit && $pembelian['id_supplier'] == $sup['id_supplier']) ? 'selected' : ''; ?>>
                                  <?php echo htmlspecialchars($sup['nama_supplier']); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="total">Total <span class="text-danger">*</span></label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;">Rp</span>
                            </div>
                            <input type="number" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   id="total" 
                                   name="total" 
                                   value="<?php echo $mode_edit ? $pembelian['total'] : '0'; ?>" 
                                   placeholder="Total pembelian" 
                                   required
                                   min="0"
                                   step="100"
                                   readonly>
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label>Tanggal</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-primary text-white" style="height: 40px; display: flex; align-items: center; font-size: 14px;"><i class="mdi mdi-calendar"></i></span>
                            </div>
                            <input type="date" 
                                   class="form-control" 
                                   style="height: 40px;"
                                   value="<?php echo $mode_edit ? date('Y-m-d', strtotime($pembelian['tanggal'])) : date('Y-m-d'); ?>" 
                                   readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mt-4">
                      <div class="col-12">
                        <h5>Detail Pembelian</h5>
                        <div class="table-responsive">
                          <table class="table table-bordered" id="table-detail">
                            <thead class="thead-light">
                              <tr>
                                <th width="5%">No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga Beli</th>
                                <th>Subtotal</th>
                                <th width="10%">Aksi</th>
                              </tr>
                            </thead>
                            <tbody id="detail-body">
                              <?php if ($mode_edit && !empty($detail_pembelian)): ?>
                                <?php $no = 1; foreach ($detail_pembelian as $index => $item): ?>
                                  <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                      <select class="form-control" name="item_barang[]" required>
                                        <option value="">Pilih Barang</option>
                                        <?php foreach ($barang as $b): ?>
                                          <option value="<?php echo $b['id_barang']; ?>" 
                                                  <?php echo ($b['id_barang'] == $item['id_barang']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($b['nama_barang']); ?> (<?php echo htmlspecialchars($b['kode_barang']); ?>)
                                          </option>
                                        <?php endforeach; ?>
                                      </select>
                                    </td>
                                    <td>
                                      <input type="number" class="form-control jumlah" name="item_jumlah[]" value="<?php echo $item['jumlah']; ?>" min="1" required>
                                    </td>
                                    <td>
                                      <input type="number" class="form-control harga_beli" name="item_harga_beli[]" value="<?php echo $item['harga_beli']; ?>" min="0" step="100" required>
                                    </td>
                                    <td>
                                      <input type="number" class="form-control subtotal" name="item_subtotal[]" value="<?php echo $item['subtotal']; ?>" readonly>
                                    </td>
                                    <td>
                                      <button type="button" class="btn btn-danger btn-sm remove-row" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                      </button>
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td>1</td>
                                  <td>
                                    <select class="form-control" name="item_barang[]" required>
                                      <option value="">Pilih Barang</option>
                                      <?php foreach ($barang as $b): ?>
                                        <option value="<?php echo $b['id_barang']; ?>"><?php echo htmlspecialchars($b['nama_barang']); ?> (<?php echo htmlspecialchars($b['kode_barang']); ?>)</option>
                                      <?php endforeach; ?>
                                    </select>
                                  </td>
                                  <td>
                                    <input type="number" class="form-control jumlah" name="item_jumlah[]" min="1" required>
                                  </td>
                                  <td>
                                    <input type="number" class="form-control harga_beli" name="item_harga_beli[]" min="0" step="100" required>
                                  </td>
                                  <td>
                                    <input type="number" class="form-control subtotal" name="item_subtotal[]" readonly>
                                  </td>
                                  <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Hapus">
                                      <i class="mdi mdi-delete"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                <td id="grand-total">0</td>
                                <td></td>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                        
                        <div class="mt-2">
                          <button type="button" class="btn btn-success btn-sm" id="add-row">
                            <i class="mdi mdi-plus"></i> Tambah Item
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mt-4">
                      <div class="col-12">
                        <div class="d-flex">
                          <button type="submit" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-content-save btn-icon-prepend"></i> Simpan
                          </button>
                          <a href="index.php?controller=pembelianBarang&action=index" class="btn btn-light btn-icon-text ml-2">
                            <i class="mdi mdi-close btn-icon-prepend"></i> Batal
                          </a>
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
    document.addEventListener('DOMContentLoaded', function() {
      const detailBody = document.getElementById('detail-body');
      const grandTotalElement = document.getElementById('grand-total');
      const totalInput = document.getElementById('total');
      let rowNumber = <?php echo $mode_edit ? count($detail_pembelian) : 1; ?>;
      
      // Fungsi untuk menghitung subtotal
      function hitungSubtotal(row) {
        const jumlah = parseFloat(row.querySelector('.jumlah').value) || 0;
        const harga = parseFloat(row.querySelector('.harga_beli').value) || 0;
        const subtotal = jumlah * harga;
        row.querySelector('.subtotal').value = subtotal;
        hitungTotal();
      }
      
      // Fungsi untuk menghitung total keseluruhan
      function hitungTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#detail-body tr').forEach(row => {
          const subtotal = parseFloat(row.querySelector('.subtotal').value) || 0;
          grandTotal += subtotal;
        });
        grandTotalElement.textContent = grandTotal.toLocaleString('id-ID');
        totalInput.value = grandTotal;
      }
      
      // Tambahkan event listeners untuk perubahan jumlah dan harga
      detailBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah') || e.target.classList.contains('harga_beli')) {
          const row = e.target.closest('tr');
          hitungSubtotal(row);
        }
      });
      
      // Tambah baris baru
      document.getElementById('add-row').addEventListener('click', function() {
        rowNumber++;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
          <td>${rowNumber}</td>
          <td>
            <select class="form-control" name="item_barang[]" required>
              <option value="">Pilih Barang</option>
              <?php foreach ($barang as $b): ?>
                <option value="<?php echo $b['id_barang']; ?>"><?php echo addslashes(htmlspecialchars($b['nama_barang'])); ?> (<?php echo addslashes(htmlspecialchars($b['kode_barang'])); ?>)</option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
            <input type="number" class="form-control jumlah" name="item_jumlah[]" min="1" required>
          </td>
          <td>
            <input type="number" class="form-control harga_beli" name="item_harga_beli[]" min="0" step="100" required>
          </td>
          <td>
            <input type="number" class="form-control subtotal" name="item_subtotal[]" readonly>
          </td>
          <td>
            <button type="button" class="btn btn-danger btn-sm remove-row" title="Hapus">
              <i class="mdi mdi-delete"></i>
            </button>
          </td>
        `;
        detailBody.appendChild(newRow);
      });
      
      // Hapus baris
      detailBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
          const row = e.target.closest('.remove-row').closest('tr');
          if (detailBody.rows.length > 1) {
            row.remove();
            rowNumber--;
            // Perbarui nomor urut setelah penghapusan
            document.querySelectorAll('#detail-body tr').forEach((row, index) => {
              row.cells[0].textContent = index + 1;
            });
            hitungTotal();
          } else {
            alert('Minimal harus ada satu item pembelian!');
          }
        }
      });
      
      // Hitung subtotal untuk semua baris yang sudah ada
      document.querySelectorAll('#detail-body tr').forEach(row => {
        hitungSubtotal(row);
      });
      
      // Validasi form sebelum submit
      const form = document.querySelector('form');
      if (form) {
        form.addEventListener('submit', function(e) {
          const id_supplier = document.getElementById('id_supplier').value;
          const total = document.getElementById('total').value.trim();
          
          // Validasi hanya untuk mode edit (karena mode tambah no_nota auto-generate)
          const no_nota_input = document.getElementById('no_nota');
          const no_nota = no_nota_input.value.trim();
          const isEditMode = !no_nota_input.readOnly;
          
          if (isEditMode && no_nota === '') {
            e.preventDefault();
            alert('No nota tidak boleh kosong!');
            document.getElementById('no_nota').focus();
            return false;
          }
          
          if (id_supplier === '') {
            e.preventDefault();
            alert('Supplier harus dipilih!');
            document.getElementById('id_supplier').focus();
            return false;
          }
          
          if (total === '' || parseFloat(total) <= 0) {
            e.preventDefault();
            alert('Total harus berupa angka positif!');
            return false;
          }
          
          // Cek apakah ada item pembelian
          const itemBarang = document.querySelectorAll('select[name="item_barang[]"]');
          let itemValid = false;
          for (let item of itemBarang) {
            if (item.value !== '') {
              itemValid = true;
              break;
            }
          }
          
          if (!itemValid) {
            e.preventDefault();
            alert('Minimal harus ada satu item pembelian!');
            return false;
          }
        });
      }
    });
  </script>
</body>
</html>