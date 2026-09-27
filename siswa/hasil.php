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

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT se.*, e.judul_ujian 
    FROM student_exams se 
    JOIN exams e ON se.exam_id = e.id 
    WHERE se.user_id = $user_id AND se.exam_id = $exam_id AND se.status = 'selesai'
"));

if (!$data) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Evaluasi - CBT Online</title>
  <script src="../assets/js/dark-mode.js?v=2"></script>
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

    .score-card {
      border: none;
      border-radius: 1.25rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
  </style>
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

  <button type="button" class="theme-toggle page-theme-toggle" data-theme-toggle aria-label="Ganti tema">
    <i data-theme-icon class="bi bi-moon-stars-fill"></i>
  </button>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card score-card p-5 text-center bg-white">
          <div class="mb-3">
            <i class="bi bi-check-circle-fill text-success display-3"></i>
          </div>
          <h4 class="fw-bold mb-1">Ujian Selesai!</h4>
          <p class="text-muted small mb-4"><?= htmlspecialchars($data['judul_ujian']) ?></p>

          <div class="bg-light rounded-4 p-4 mb-4 border">
            <span class="text-muted small fw-semibold d-block mb-1">PEROLEHAN NILAI ANDA</span>
            <h1 class="display-3 fw-bold text-primary mb-2"><?= $data['total_nilai'] ?></h1>
            <small class="text-secondary d-block">
              Diserahkan pada: <?= date('d M Y, H:i', strtotime($data['waktu_selesai'])) ?> WIB
            </small>
          </div>

          <a href="index.php" class="btn btn-primary rounded-3 w-100 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>