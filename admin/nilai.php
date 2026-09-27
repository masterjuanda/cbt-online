<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$list_exams = mysqli_query($koneksi, "SELECT * FROM exams ORDER BY id DESC");

if ($exam_id == 0) {
  $first_exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM exams ORDER BY id DESC LIMIT 1"));
  if ($first_exam) {
    $exam_id = $first_exam['id'];
  }
}

$detail_exam = null;
$list_nilai = null;

if ($exam_id > 0) {
  $detail_exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM exams WHERE id = $exam_id"));
  $list_nilai  = mysqli_query($koneksi, "
        SELECT se.*, u.nama_lengkap, u.username 
        FROM student_exams se 
        JOIN users u ON se.user_id = u.id 
        WHERE se.exam_id = $exam_id AND se.status = 'selesai'
        ORDER BY se.total_nilai DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Nilai - CBT Online</title>
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

    @media print {

      .no-print,
      .sidebar {
        display: none !important;
      }

      body {
        background: #fff !important;
      }

      .content-area {
        width: 100% !important;
        padding: 0 !important;
      }
    }
  </style>
  <link rel="stylesheet" href="../assets/css/admin-responsive.css?v=3">
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
</head>

<body>

  <div class="d-flex">
    <?php include 'partials/navigation.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 content-area" style="min-height: 100vh;">
      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print admin-page-titlebar">
        <div>
          <h4 class="fw-bold mb-0">Rekapitulasi Nilai Siswa</h4>
          <p class="text-muted small mb-0">Evaluasi performa ujian dan cetak laporan resmi</p>
        </div>
        <?php if ($detail_exam && mysqli_num_rows($list_nilai) > 0): ?>
          <button onclick="window.print()" class="btn btn-outline-primary rounded-3 shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Ekspor PDF
          </button>
        <?php endif; ?>
      </div>

      <!-- Filter Ujian -->
      <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white no-print">
        <form action="" method="GET" class="row g-2 align-items-center">
          <div class="col-auto">
            <label class="small fw-bold text-secondary">Pilih Ujian:</label>
          </div>
          <div class="col-md-6">
            <select name="exam_id" class="form-select rounded-3 admin-mobile-select" aria-label="Pilih paket ujian" onchange="this.form.submit()">
              <?php while ($e = mysqli_fetch_assoc($list_exams)): ?>
                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $exam_id) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['judul_ujian']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </form>
      </div>

      <!-- Tampilan Header Saat Dicetak -->
      <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
        <h4 class="fw-bold mb-1">LAPORAN HASIL EVALUASI UJIAN</h4>
        <h5 class="text-uppercase mb-1"><?= htmlspecialchars($detail_exam['judul_ujian'] ?? '') ?></h5>
        <small class="text-muted">Tanggal Cetak: <?= date('d M Y, H:i') ?> WIB</small>
      </div>

      <!-- Tabel Nilai -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
              <tr class="small text-secondary">
                <th class="ps-4">PERINGKAT</th>
                <th>NISN / USERNAME</th>
                <th>NAMA SISWA</th>
                <th>WAKTU SELESAI</th>
                <th class="text-end pe-4">NILAI AKHIR</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($detail_exam && mysqli_num_rows($list_nilai) > 0): ?>
                <?php $rank = 1;
                while ($n = mysqli_fetch_assoc($list_nilai)): ?>
                  <tr>
                    <td class="ps-4">
                      <span class="badge <?= $rank <= 3 ? 'bg-warning text-dark' : 'bg-light text-secondary border' ?> rounded-pill px-2 py-1">
                        #<?= $rank++ ?>
                      </span>
                    </td>
                    <td class="fw-semibold text-secondary"><?= htmlspecialchars($n['username']) ?></td>
                    <td class="fw-bold text-dark"><?= htmlspecialchars($n['nama_lengkap']) ?></td>
                    <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['waktu_selesai'])) ?> WIB</small></td>
                    <td class="text-end pe-4">
                      <span class="fs-6 fw-bold <?= $n['total_nilai'] >= 75 ? 'text-success' : 'text-danger' ?>">
                        <?= $n['total_nilai'] ?>
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted small">Belum ada siswa yang menyelesaikan ujian pada paket ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php include 'modal/modal_logout.php'; ?>
  <script src="../assets/js/admin-mobile-select.js?v=2"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>