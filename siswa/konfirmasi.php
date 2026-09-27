<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

$exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM exams WHERE id = $exam_id AND status = 'aktif'"));
if (!$exam) {
  header("Location: index.php");
  exit;
}

// Cek apakah siswa sudah pernah menyelesaikan ujian ini
$cek_selesai = mysqli_query($koneksi, "SELECT * FROM student_exams WHERE user_id = $user_id AND exam_id = $exam_id AND status = 'selesai'");
if (mysqli_num_rows($cek_selesai) > 0) {
  header("Location: index.php");
  exit;
}

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mulai'])) {
  $token_input = strtoupper(trim($_POST['token']));

  if ($token_input === $exam['token']) {
    // Cek apakah sudah ada sesi mengerjakan sebelumnya
    $cek_sesi = mysqli_query($koneksi, "SELECT * FROM student_exams WHERE user_id = $user_id AND exam_id = $exam_id");
    if (mysqli_num_rows($cek_sesi) === 0) {
      $waktu_mulai = date('Y-m-d H:i:s');
      mysqli_query($koneksi, "INSERT INTO student_exams (user_id, exam_id, waktu_mulai, status) VALUES ($user_id, $exam_id, '$waktu_mulai', 'mengerjakan')");
    }
    header("Location: ujian.php?exam_id=" . $exam_id);
    exit;
  } else {
    $pesan_error = 'Token yang Anda masukkan tidak valid! Silakan minta token ke pengawas.';
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Ujian - CBT Online</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Konfirmasi Tes</span>
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($exam['judul_ujian']) ?></h4>
            <p class="text-muted small">Harap perhatikan petunjuk sebelum memulai pengerjaan.</p>
          </div>

          <div class="bg-light rounded-3 p-3 mb-4 small text-secondary">
            <div class="d-flex justify-content-between mb-2">
              <span>Alokasi Waktu:</span>
              <strong class="text-dark"><?= $exam['durasi_menit'] ?> Menit</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Nama Siswa:</span>
              <strong class="text-dark"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>
            </div>
            <div class="d-flex justify-content-between">
              <span>Sistem Timer:</span>
              <strong class="text-danger">Auto-Submit otomatis saat waktu habis</strong>
            </div>
          </div>

          <?php if (!empty($pesan_error)): ?>
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
              <i class="bi bi-exclamation-circle-fill me-1"></i> <?= htmlspecialchars($pesan_error) ?>
            </div>
          <?php endif; ?>

          <form action="" method="POST">
            <div class="mb-4">
              <label class="form-label small fw-semibold text-secondary">Masukkan Token Ujian</label>
              <input type="text" name="token" class="form-control form-control-lg text-center rounded-3 fw-bold text-uppercase tracking-wider" placeholder="Ketik token di sini" required autofocus>
            </div>

            <div class="d-flex gap-2">
              <a href="index.php" class="btn btn-light rounded-3 w-50">Kembali</a>
              <button type="submit" name="mulai" class="btn btn-primary rounded-3 w-50 fw-semibold">Mulai Kerjakan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>