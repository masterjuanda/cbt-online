<?php
session_start();
require_once '../config/database.php';
global $koneksi;

// Proteksi Halaman Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Mengambil Statistik Sistem
$total_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'siswa'"))['total'];
$total_ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM exams"))['total'];
$total_soal  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM questions"))['total'];
$total_ikut  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM student_exams WHERE status = 'selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - CBT Online</title>
  <!-- Fonts & Bootstrap 5 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
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
      transition: all 0.2s ease;
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

    .stat-card {
      border: none;
      border-radius: 1rem;
      background: #ffffff;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-3px);
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
  </style>
  <link rel="stylesheet" href="../assets/css/admin-responsive.css?v=2">
</head>

<body>

  <div class="d-flex">
    <?php include 'partials/navigation.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow-1 p-4" style="min-height: 100vh;">
      <!-- Topbar -->
      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom admin-page-titlebar">
        <div>
          <h4 class="fw-bold mb-0">Dashboard Ringkasan</h4>
          <p class="text-muted small mb-0">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong></p>
        </div>
        <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
          <i class="bi bi-shield-lock-fill me-1"></i> Mode Administrator
        </div>
      </div>

      <!-- 4 Grid Stat Cards -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-journal-bookmark-fill"></i>
              </div>
              <div>
                <small class="text-muted fw-semibold">Total Ujian</small>
                <h3 class="fw-bold mb-0"><?= $total_ujian ?></h3>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                <i class="bi bi-question-square-fill"></i>
              </div>
              <div>
                <small class="text-muted fw-semibold">Bank Soal</small>
                <h3 class="fw-bold mb-0"><?= $total_soal ?></h3>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-people-fill"></i>
              </div>
              <div>
                <small class="text-muted fw-semibold">Siswa Terdaftar</small>
                <h3 class="fw-bold mb-0"><?= $total_siswa ?></h3>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card stat-card p-3">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                <i class="bi bi-check2-circle"></i>
              </div>
              <div>
                <small class="text-muted fw-semibold">Ujian Selesai</small>
                <h3 class="fw-bold mb-0"><?= $total_ikut ?></h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shortcut Action Box -->
      <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <h5 class="fw-bold mb-2">Panduan Pengoperasian</h5>
        <p class="text-muted small">Ikuti langkah berurutan di bawah ini untuk memulai proses evaluasi:</p>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-bold text-dark mb-1">1. Buat Paket Ujian</div>
              <p class="text-muted small mb-2">Tentukan judul mata pelajaran, durasi waktu, serta token ujian.</p>
              <a href="ujian.php" class="btn btn-sm btn-outline-primary rounded-2">Buka Paket Ujian</a>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-bold text-dark mb-1">2. Tambahkan Bank Soal</div>
              <p class="text-muted small mb-2">Masukkan butir soal pilihan ganda A-E dan tetapkan kunci jawaban.</p>
              <a href="soal.php" class="btn btn-sm btn-outline-primary rounded-2">Kelola Soal</a>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border rounded-3 p-3 h-100">
              <div class="fw-bold text-dark mb-1">3. Lihat Rekap Nilai</div>
              <p class="text-muted small mb-2">Pantau hasil pengerjaan siswa dan cetak/ekspor lembar evaluasi.</p>
              <a href="nilai.php" class="btn btn-sm btn-outline-primary rounded-2">Lihat Nilai</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'modal/modal_logout.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>