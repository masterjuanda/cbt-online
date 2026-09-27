<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$adminNavigation = [
  ['file' => 'index.php', 'label' => 'Dashboard', 'icon' => 'bi-grid-fill'],
  ['file' => 'ujian.php', 'label' => 'Paket Ujian', 'icon' => 'bi-journal-check'],
  ['file' => 'soal.php', 'label' => 'Bank Soal', 'icon' => 'bi-question-circle'],
  ['file' => 'siswa.php', 'label' => 'Data Siswa', 'icon' => 'bi-people'],
  ['file' => 'nilai.php', 'label' => 'Rekap Nilai', 'icon' => 'bi-trophy'],
];
?>
<header class="admin-mobile-header no-print">
  <button class="admin-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileMenu" aria-controls="adminMobileMenu" aria-label="Buka menu navigasi">
    <i class="bi bi-list"></i>
  </button>
  <a class="admin-mobile-brand" href="index.php">
    <span class="admin-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
    <span><strong>CBT Portal</strong><small>Panel Administrator</small></span>
  </a>
  <button type="button" class="theme-toggle admin-header-theme-toggle" data-theme-toggle aria-label="Ganti tema">
    <i data-theme-icon class="bi bi-moon-stars-fill"></i>
  </button>
  <span class="admin-user-avatar" title="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator') ?>">
    <?= htmlspecialchars(mb_strtoupper(mb_substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1))) ?>
  </span>
</header>

<aside class="sidebar admin-sidebar p-3 d-none d-md-flex flex-column text-white flex-shrink-0 no-print">
  <a class="admin-sidebar-brand d-flex align-items-center mb-4 px-2 pt-2" href="index.php">
    <span class="admin-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
    <span><strong>CBT Portal</strong><small>Panel Administrator</small></span>
  </a>
  <div class="admin-nav-caption">MENU UTAMA</div>
  <ul class="nav nav-pills flex-column mb-auto">
    <?php foreach ($adminNavigation as $item): ?>
      <li class="nav-item">
        <a href="<?= htmlspecialchars($item['file']) ?>" class="nav-link <?= $currentPage === $item['file'] ? 'active' : '' ?>" <?= $currentPage === $item['file'] ? 'aria-current="page"' : '' ?>>
          <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i><span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="admin-sidebar-footer">
    <button type="button" class="admin-theme-button" data-theme-toggle aria-label="Ganti tema">
      <i data-theme-icon class="bi bi-moon-stars-fill"></i><span>Tema tampilan</span>
    </button>
    <div class="admin-user-card">
      <span class="admin-user-avatar"><i class="bi bi-person-fill"></i></span>
      <span class="admin-user-details"><strong><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator') ?></strong><small>Administrator</small></span>
    </div>
    <button type="button" class="admin-logout-button" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout">
      <i class="bi bi-box-arrow-right"></i><span>Keluar Sistem</span>
    </button>
  </div>
</aside>

<div class="offcanvas offcanvas-start admin-drawer no-print" tabindex="-1" id="adminMobileMenu" aria-labelledby="adminMobileMenuLabel">
  <div class="offcanvas-header admin-drawer-header">
    <a class="admin-sidebar-brand d-flex align-items-center" href="index.php" id="adminMobileMenuLabel">
      <span class="admin-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
      <span><strong>CBT Portal</strong><small>Panel Administrator</small></span>
    </a>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div class="admin-nav-caption">MENU UTAMA</div>
    <ul class="nav nav-pills flex-column mb-auto">
      <?php foreach ($adminNavigation as $item): ?>
        <li class="nav-item">
          <a href="<?= htmlspecialchars($item['file']) ?>" class="nav-link <?= $currentPage === $item['file'] ? 'active' : '' ?>" <?= $currentPage === $item['file'] ? 'aria-current="page"' : '' ?>>
            <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i><span><?= htmlspecialchars($item['label']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="admin-sidebar-footer">
      <button type="button" class="admin-theme-button" data-theme-toggle aria-label="Ganti tema">
        <i data-theme-icon class="bi bi-moon-stars-fill"></i><span>Tema tampilan</span>
      </button>
      <div class="admin-user-card">
        <span class="admin-user-avatar"><i class="bi bi-person-fill"></i></span>
        <span class="admin-user-details"><strong><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator') ?></strong><small>Administrator</small></span>
      </div>
      <button type="button" class="admin-logout-button" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout" data-bs-dismiss="offcanvas">
        <i class="bi bi-box-arrow-right"></i><span>Keluar Sistem</span>
      </button>
    </div>
  </div>
</div>