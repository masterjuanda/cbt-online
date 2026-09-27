<?php
session_start();
require_once '../config/database.php';
global $koneksi;

// Proteksi Halaman Siswa
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Mengambil Daftar Ujian Aktif beserta status pengerjaan siswa
$query_ujian = mysqli_query($koneksi, "
    SELECT e.*, 
           (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) AS total_soal,
           se.status AS status_siswa,
           se.total_nilai
    FROM exams e
    LEFT JOIN student_exams se ON se.exam_id = e.id AND se.user_id = $user_id
    WHERE e.status = 'aktif'
    ORDER BY e.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Siswa - CBT Online</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
    }

    .navbar-custom {
      background: #0f172a;
    }

    .exam-card {
      border: none;
      border-radius: 1rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .exam-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

  <!-- Navbar Siswa -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-3 py-3 mb-4 shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
        <i class="bi bi-mortarboard-fill text-primary fs-4 me-2"></i> CBT Siswa
      </a>
      <div class="d-flex align-items-center gap-3">
        <span class="text-light small d-none d-sm-inline">
          Halo, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong> (<?= htmlspecialchars($_SESSION['username']) ?>)
        </span>
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout">Keluar</button>
      </div>
    </div>
  </nav>

  <div class="container pb-5">
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Daftar Ujian Tersedia</h4>
      <p class="text-muted small">Pilih paket ujian di bawah ini dan masukkan token ujian dari pengawas untuk memulai.</p>
    </div>

    <div class="row g-4">
      <?php if (mysqli_num_rows($query_ujian) > 0): ?>
        <?php while ($u = mysqli_fetch_assoc($query_ujian)): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card exam-card shadow-sm h-100 p-4 bg-white d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1 rounded-pill">
                    <i class="bi bi-clock me-1"></i> <?= $u['durasi_menit'] ?> Menit
                  </span>
                  <?php if ($u['status_siswa'] === 'selesai'): ?>
                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill">Selesai Dikerjakan</span>
                  <?php elseif ($u['status_siswa'] === 'mengerjakan'): ?>
                    <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1 rounded-pill">Sedang Berlangsung</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Belum Mengikuti</span>
                  <?php endif; ?>
                </div>

                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($u['judul_ujian']) ?></h5>
                <p class="text-muted small mb-3">
                  <i class="bi bi-file-earmark-text me-1"></i> <?= $u['total_soal'] ?> Butir Pertanyaan
                </p>
              </div>

              <div class="pt-3 border-top mt-3">
                <?php if ($u['status_siswa'] === 'selesai'): ?>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Nilai Akhir:</span>
                    <span class="fw-bold text-primary fs-5"><?= $u['total_nilai'] ?></span>
                  </div>
                <?php else: ?>
                  <a href="konfirmasi.php?exam_id=<?= $u['id'] ?>" class="btn btn-primary w-100 rounded-3 fw-semibold">
                    <?= ($u['status_siswa'] === 'mengerjakan') ? 'Lanjutkan Ujian' : 'Mulai Ujian' ?> <i class="bi bi-arrow-right-short fs-5 align-middle"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
            <i class="bi bi-inbox text-muted display-4 mb-2"></i>
            <h6 class="fw-bold">Tidak Ada Ujian Aktif</h6>
            <p class="text-muted small mb-0">Saat ini belum ada jadwal evaluasi yang diaktifkan oleh admin/guru.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'modal/modal_logout.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>