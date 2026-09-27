<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM exams WHERE id = $exam_id"));

if (!$exam) {
  die("Paket ujian tidak ditemukan!");
}

$questions = mysqli_query($koneksi, "SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Naskah Soal - <?= htmlspecialchars($exam['judul_ujian']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 12pt;
      color: #000;
    }

    .kop-surat {
      border-bottom: 3px double #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    @media print {
      .no-print {
        display: none !important;
      }

      body {
        padding: 0;
      }
    }
  </style>
</head>

<body class="p-4">

  <div class="no-print mb-4 d-flex justify-content-between">
    <a href="soal.php?exam_id=<?= $exam_id ?>" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    <button onclick="window.print()" class="btn btn-sm btn-primary">Simpan sebagai PDF / Cetak</button>
  </div>

  <!-- Header Naskah Ujian -->
  <div class="text-center kop-surat">
    <h4 class="fw-bold mb-1">EVALUASI COMPUTER BASED TEST (CBT)</h4>
    <h5 class="mb-1 text-uppercase"><?= htmlspecialchars($exam['judul_ujian']) ?></h5>
    <p class="mb-0 small">Alokasi Waktu: <?= $exam['durasi_menit'] ?> Menit | Sifat Ujian: Mandiri / Daring</p>
  </div>

  <!-- Daftar Soal -->
  <div class="mt-4">
    <?php $no = 1;
    while ($q = mysqli_fetch_assoc($questions)): ?>
      <div class="mb-4" style="page-break-inside: avoid;">
        <div class="d-flex">
          <span class="me-2 fw-bold"><?= $no++ ?>.</span>
          <div>
            <p class="mb-2"><?= nl2br(htmlspecialchars($q['pertanyaan'])) ?></p>
            <div class="ps-2">
              <div>A. <?= htmlspecialchars($q['opsi_a']) ?></div>
              <div>B. <?= htmlspecialchars($q['opsi_b']) ?></div>
              <div>C. <?= htmlspecialchars($q['opsi_c']) ?></div>
              <div>D. <?= htmlspecialchars($q['opsi_d']) ?></div>
              <div>E. <?= htmlspecialchars($q['opsi_e']) ?></div>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

</body>

</html>