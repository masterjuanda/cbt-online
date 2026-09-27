<?php
session_start();
require_once '../config/database.php';
global $koneksi;

// Proteksi Halaman Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$pesan = '';

// Proses Tambah Ujian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_ujian'])) {
  $judul_ujian  = mysqli_real_escape_string($koneksi, trim($_POST['judul_ujian']));
  $durasi_menit = (int)$_POST['durasi_menit'];
  $token        = strtoupper(mysqli_real_escape_string($koneksi, trim($_POST['token'])));

  $query = mysqli_query($koneksi, "INSERT INTO exams (judul_ujian, durasi_menit, token, status) VALUES ('$judul_ujian', '$durasi_menit', '$token', 'aktif')");
  if ($query) {
    $pesan = 'Paket ujian berhasil ditambahkan!';
  }
}

// Proses Hapus Ujian
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM exams WHERE id = $id");
  header("Location: ujian.php");
  exit;
}

// Mengambil Data Seluruh Ujian
$list_ujian = mysqli_query($koneksi, "SELECT e.*, (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) AS total_soal FROM exams e ORDER BY e.id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paket Ujian - CBT Online</title>
  <script src="../assets/js/dark-mode.js?v=2"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
    }

    .sidebar {
      width: 260px;
      background: #0f172a;
      min-height: 100vh;
    }

    .sidebar .nav-link {
      color: #94a3b8;
      font-weight: 500;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      margin-bottom: 0.25rem;
      display: flex;
      align-items: center;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      color: #ffffff;
      background: #2563eb;
    }

    .sidebar .nav-link i {
      font-size: 1.2rem;
      margin-right: 0.75rem;
    }
  </style>
  <link rel="stylesheet" href="../assets/css/admin-responsive.css?v=5">
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
</head>

<body>

  <div class="d-flex">
    <?php include 'partials/navigation.php'; ?>

    <!-- Content -->
    <div class="flex-grow-1 p-4" style="min-height: 100vh;">
      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom admin-page-titlebar">
        <div>
          <h4 class="fw-bold mb-0">Manajemen Paket Ujian</h4>
          <p class="text-muted small mb-0">Konfigurasi jadwal, token pengawas, dan durasi pengerjaan</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUjian">
          <i class="bi bi-plus-circle me-1"></i> Buat Ujian Baru
        </button>
      </div>

      <?php if (!empty($pesan)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($pesan) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Tabel Daftar Ujian -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
              <tr class="small text-secondary">
                <th class="ps-4">NAMA UJIAN</th>
                <th>DURASI</th>
                <th>TOKEN</th>
                <th>JUMLAH SOAL</th>
                <th>STATUS</th>
                <th class="text-end pe-4">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($list_ujian) > 0): ?>
                <?php while ($u = mysqli_fetch_assoc($list_ujian)): ?>
                  <tr>
                    <td class="ps-4 fw-semibold text-dark"><?= htmlspecialchars($u['judul_ujian']) ?></td>
                    <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> <?= $u['durasi_menit'] ?> Menit</span></td>
                    <td><span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1"><?= $u['token'] ?></span></td>
                    <td><span class="badge bg-info-subtle text-info fw-semibold"><?= $u['total_soal'] ?> Butir</span></td>
                    <td>
                      <span class="badge <?= $u['status'] === 'aktif' ? 'bg-success' : 'bg-danger' ?> rounded-pill">
                        <?= ucfirst($u['status']) ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <a href="soal.php?exam_id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary rounded-2 me-1" title="Kelola Soal">
                        <i class="bi bi-list-task"></i> Soal
                      </a>
                      <button type="button"
                        class="action-icon-btn action-icon-btn--delete btn-hapus-ujian"
                        data-id="<?= $u['id'] ?>"
                        data-judul="<?= htmlspecialchars($u['judul_ujian']) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalKonfirmasiHapus"
                        title="Hapus Ujian"
                        aria-label="Hapus ujian">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted small">Belum ada paket ujian yang dibuat. Silakan klik tombol "Buat Ujian Baru".</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Ujian -->
  <div class="modal fade" id="modalTambahUjian" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="fw-bold">Buat Paket Ujian Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Nama / Mata Pelajaran Ujian</label>
              <input type="text" name="judul_ujian" class="form-control rounded-3" placeholder="Contoh: Pemrograman Web Dasar" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Durasi Pengerjaan (Menit)</label>
              <input type="number" name="durasi_menit" class="form-control rounded-3" placeholder="Contoh: 60" min="5" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Token Akses Siswa</label>
              <div class="input-group">
                <input type="text" name="token" id="inputToken" class="form-control rounded-start-3 text-uppercase" placeholder="Contoh: CBT2026" maxlength="10" required>
                <button type="button" class="btn btn-outline-secondary rounded-end-3" onclick="document.getElementById('inputToken').value = Math.random().toString(36).substring(2, 8).toUpperCase()">Acak Token</button>
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">Token diberikan ke siswa saat ujian hendak dimulai.</small>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_ujian" class="btn btn-primary rounded-3 px-4">Simpan Paket</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'modal/modal_logout.php'; ?>
  <?php include 'modal/modal_hapus_ujian.php'; ?>

  <script>
    // Menangani klik tombol hapus agar nama ujian dan link URL-nya dinamis
    document.querySelectorAll('.btn-hapus-ujian').forEach(button => {
      button.addEventListener('click', function() {
        const idUjian = this.getAttribute('data-id');
        const judulUjian = this.getAttribute('data-judul');

        // Pasang nama ujian ke modal
        document.getElementById('teksNamaUjian').innerText = judulUjian;

        // Ubah URL link tombol konfirmasi hapus
        document.getElementById('btnEksekusiHapus').setAttribute('href', 'ujian.php?hapus=' + idUjian);
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>