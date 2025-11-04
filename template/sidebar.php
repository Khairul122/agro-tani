<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <!-- Dashboard - Semua role -->
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=dashboard&action=index">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <!-- Kategori Barang - Admin only -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=kategoriBarang&action=index">
        <i class="mdi mdi-format-list-bulleted-type menu-icon"></i>
        <span class="menu-title">Kategori Barang</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Data Barang - Admin only -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=barang&action=index">
        <i class="mdi mdi-package-variant menu-icon"></i>
        <span class="menu-title">Data Barang</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Data Supplier - Admin only -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=supplier&action=index">
        <i class="mdi mdi-truck menu-icon"></i>
        <span class="menu-title">Data Supplier</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Data Pelanggan - Admin & Kasir -->
    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=pelanggan&action=index">
        <i class="mdi mdi-account-group menu-icon"></i>
        <span class="menu-title">Data Pelanggan</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Data User - Admin only -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=user&action=index">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Data User</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Pembelian Barang - Admin only -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=pembelianBarang&action=index">
        <i class="mdi mdi-cart-plus menu-icon"></i>
        <span class="menu-title">Pembelian Barang</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Penjualan Barang - Admin & Kasir -->
    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'kasir'])): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=penjualanBarang&action=index">
        <i class="mdi mdi-cash-register menu-icon"></i>
        <span class="menu-title">Kasir</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Laporan - Admin & Owner -->
    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'owner'])): ?>
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=laporan&action=index">
        <i class="mdi mdi-file-chart menu-icon"></i>
        <span class="menu-title">Cetak Laporan</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</nav>