<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

$exam = mysqli_fetch_assoc(mysqli_query($koneksi, "
  SELECT e.*, (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) AS total_soal
  FROM exams e
  WHERE e.id = $exam_id AND e.status = 'aktif'
"));

if (!$exam) {
  header("Location: index.php");
  exit;
}

$cek_selesai = mysqli_query($koneksi, "SELECT id FROM student_exams WHERE user_id = $user_id AND exam_id = $exam_id AND status = 'selesai'");
if (mysqli_num_rows($cek_selesai) > 0) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Petunjuk Ujian - CBT Portal</title>
  <script src="../assets/js/dark-mode.js?v=2"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .guide-shell {
      width: min(100% - 2rem, 900px);
      margin: 0 auto;
    }

    .guide-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1.35rem 0;
    }

    .guide-brand {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      color: var(--app-text);
      font-weight: 700;
      text-decoration: none;
    }

    .guide-brand-mark {
      display: grid;
      width: 42px;
      height: 42px;
      place-items: center;
      border-radius: 14px;
      background: linear-gradient(145deg, #3982ff, #2157ce);
      box-shadow: 0 8px 18px rgba(37, 99, 235, 0.24);
      color: #fff;
      font-size: 1.15rem;
    }

    .guide-hero {
      position: relative;
      overflow: hidden;
      padding: clamp(1.5rem, 5vw, 2.8rem);
      border: 1px solid rgba(137, 172, 243, 0.22);
      border-radius: 1.6rem;
      background: linear-gradient(130deg, #101c36 0%, #182c55 55%, #254b87 100%);
      box-shadow: 0 22px 55px rgba(22, 44, 85, 0.2);
      color: #fff;
    }

    .guide-hero::before {
      position: absolute;
      top: -6.8rem;
      right: -3rem;
      width: 17rem;
      height: 17rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 50%;
      box-shadow: 0 0 0 1.6rem rgba(255, 255, 255, 0.035), 0 0 0 3.2rem rgba(255, 255, 255, 0.025);
      content: '';
      pointer-events: none;
    }

    .guide-kicker {
      color: #93c5fd;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.13em;
    }

    .guide-hero h1 {
      max-width: 650px;
      margin: 0.7rem 0;
      font-size: clamp(1.8rem, 5vw, 2.65rem);
      font-weight: 700;
      letter-spacing: -0.055em;
    }

    .guide-hero p {
      max-width: 630px;
      margin-bottom: 0;
      color: #c9d6ed;
      line-height: 1.7;
    }

    .exam-facts {
      display: flex;
      flex-wrap: wrap;
      gap: 0.65rem;
      margin-top: 1.5rem;
    }

    .exam-fact {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 0.85rem;
      border: 1px solid rgba(191, 219, 254, 0.2);
      border-radius: 0.85rem;
      background: rgba(5, 17, 39, 0.27);
      color: #eaf2ff;
      font-size: 0.82rem;
      font-weight: 600;
    }

    .guide-section-title {
      color: var(--app-text);
      letter-spacing: -0.035em;
    }

    .instruction-card {
      display: flex;
      height: 100%;
      gap: 1rem;
      padding: 1.15rem;
      border: 1px solid var(--app-border);
      border-radius: 1.1rem;
      background: linear-gradient(145deg, var(--app-surface), var(--app-surface-muted));
      box-shadow: var(--app-shadow);
      transition: transform 180ms ease, border-color 180ms ease;
    }

    .instruction-card:hover {
      transform: translateY(-3px);
      border-color: rgba(53, 99, 246, 0.4);
    }

    .instruction-icon {
      display: grid;
      width: 44px;
      height: 44px;
      flex: 0 0 44px;
      place-items: center;
      border-radius: 14px;
      background: rgba(53, 99, 246, 0.11);
      color: #3972eb;
      font-size: 1.15rem;
    }

    .instruction-card h3 {
      margin: 0.1rem 0 0.35rem;
      color: var(--app-text);
      font-size: 0.95rem;
      font-weight: 700;
    }

    .instruction-card p {
      margin: 0;
      color: var(--app-muted);
      font-size: 0.82rem;
      line-height: 1.6;
    }

    .guide-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 0.75rem;
      margin-top: 1.7rem;
      padding-top: 1.3rem;
      border-top: 1px solid var(--app-border);
    }

    @media (max-width: 575.98px) {
      .guide-shell {
        width: min(100% - 1.25rem, 900px);
      }

      .guide-actions>* {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <main class="guide-shell pb-5">
    <header class="guide-topbar">
      <a class="guide-brand" href="index.php">
        <span class="guide-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
        <span>CBT Portal<small class="d-block text-muted fw-normal" style="font-size: 0.68rem;">Area peserta ujian</small></span>
      </a>
      <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
        <i data-theme-icon class="bi bi-moon-stars-fill"></i>
      </button>
    </header>

    <section class="guide-hero mb-4">
      <span class="guide-kicker"><i class="bi bi-shield-check me-1"></i> SEBELUM MEMULAI</span>
      <h1>Siapkan fokusmu untuk sesi ujian</h1>
      <p>Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>. Luangkan sebentar untuk membaca panduan ini agar pengerjaan berjalan lancar dan jawabanmu tersimpan dengan baik.</p>
      <div class="exam-facts" aria-label="Ringkasan ujian">
        <span class="exam-fact"><i class="bi bi-journal-text text-info"></i><?= htmlspecialchars($exam['judul_ujian']) ?></span>
        <span class="exam-fact"><i class="bi bi-patch-question text-info"></i><?= (int)$exam['total_soal'] ?> soal</span>
        <span class="exam-fact"><i class="bi bi-clock-history text-info"></i><?= (int)$exam['durasi_menit'] ?> menit</span>
      </div>
    </section>

    <section aria-labelledby="guideInstructionsTitle">
      <div class="mb-3">
        <span class="text-primary small fw-bold text-uppercase" style="letter-spacing: 0.1em;">Checklist peserta</span>
        <h2 class="guide-section-title h4 fw-bold mt-1 mb-0" id="guideInstructionsTitle">Pastikan semuanya siap</h2>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <article class="instruction-card">
            <span class="instruction-icon"><i class="bi bi-wifi"></i></span>
            <div>
              <h3>Gunakan koneksi yang stabil</h3>
              <p>Usahakan tetap memakai perangkat dan jaringan yang sama hingga ujian selesai.</p>
            </div>
          </article>
        </div>
        <div class="col-md-6">
          <article class="instruction-card">
            <span class="instruction-icon"><i class="bi bi-arrow-clockwise"></i></span>
            <div>
              <h3>Jangan muat ulang halaman</h3>
              <p>Hindari menutup tab atau me-refresh halaman agar proses ujian tidak terganggu.</p>
            </div>
          </article>
        </div>
        <div class="col-md-6">
          <article class="instruction-card">
            <span class="instruction-icon"><i class="bi bi-stopwatch"></i></span>
            <div>
              <h3>Perhatikan hitung mundur</h3>
              <p>Timer terus berjalan. Saat waktu habis, sistem akan mengumpulkan jawaban secara otomatis.</p>
            </div>
          </article>
        </div>
        <div class="col-md-6">
          <article class="instruction-card">
            <span class="instruction-icon"><i class="bi bi-check2-square"></i></span>
            <div>
              <h3>Pilih jawaban dengan teliti</h3>
              <p>Periksa kembali jawaban sebelum menyelesaikan ujian. Jawaban yang belum dipilih dianggap kosong.</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <div class="guide-actions">
      <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke daftar ujian
      </a>
      <a href="konfirmasi.php?exam_id=<?= (int)$exam['id'] ?>" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
        Lanjutkan ke verifikasi token<i class="bi bi-arrow-right ms-2"></i>
      </a>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>