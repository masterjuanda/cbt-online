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
$jumlah_ujian_aktif = mysqli_num_rows($query_ujian);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Siswa - CBT Online</title>
  <script src="../assets/js/dark-mode.js?v=2"></script>
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
      position: relative;
      z-index: 1030;
      background: #0f172a;
    }

    .student-account-toggle {
      display: grid;
      width: 42px;
      height: 42px;
      place-items: center;
      border: 1px solid rgba(148, 163, 184, 0.28);
      border-radius: 14px;
      background: rgba(148, 163, 184, 0.1);
      color: #dbeafe;
      font-size: 1.1rem;
      transition: background-color 160ms ease, transform 160ms ease;
    }

    .student-account-toggle:hover,
    .student-account-toggle[aria-expanded="true"] {
      transform: translateY(-1px);
      background: rgba(59, 130, 246, 0.26);
      color: #fff;
    }

    .student-account-menu {
      right: 0;
      left: auto;
      min-width: 220px;
      margin-top: 0 !important;
      padding: 0.55rem;
      border: 1px solid var(--app-border);
      border-radius: 1rem;
      box-shadow: 0 16px 38px rgba(15, 23, 42, 0.18);
    }

    @media (hover: hover) and (pointer: fine) {

      .dropdown:hover>.student-account-menu,
      .dropdown:focus-within>.student-account-menu {
        display: block;
      }
    }

    .welcome-panel {
      position: relative;
      isolation: isolate;
      overflow: hidden;
      padding: clamp(1.5rem, 4vw, 2.4rem);
      border: 1px solid rgba(148, 181, 255, 0.22);
      border-radius: 1.5rem;
      background: linear-gradient(115deg, #132346 0%, #1c3c78 62%, #2856a2 100%);
      box-shadow: 0 20px 46px rgba(22, 54, 112, 0.2);
      color: #fff;
    }

    .welcome-panel::after {
      position: absolute;
      z-index: -1;
      top: -7rem;
      right: -3rem;
      width: 20rem;
      height: 20rem;
      border: 1px solid rgba(255, 255, 255, 0.13);
      border-radius: 50%;
      box-shadow: 0 0 0 2rem rgba(255, 255, 255, 0.035), 0 0 0 4rem rgba(255, 255, 255, 0.025);
      content: "";
      pointer-events: none;
    }

    .welcome-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      margin-bottom: 1rem;
      padding: 0.4rem 0.75rem;
      border: 1px solid rgba(191, 219, 254, 0.2);
      border-radius: 999px;
      background: rgba(9, 22, 49, 0.35);
      color: #bfdbfe;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.04em;
    }

    .welcome-panel h1 {
      max-width: 680px;
      font-size: clamp(1.7rem, 4vw, 2.55rem);
      font-weight: 700;
      letter-spacing: -0.055em;
    }

    .welcome-panel p {
      max-width: 650px;
      margin-bottom: 0;
      color: #d3def2;
      line-height: 1.7;
    }

    .welcome-stat {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      margin-top: 1.35rem;
      padding: 0.65rem 0.85rem;
      border: 1px solid rgba(191, 219, 254, 0.2);
      border-radius: 0.9rem;
      background: rgba(9, 22, 49, 0.28);
      color: #eaf2ff;
      font-size: 0.82rem;
      font-weight: 600;
    }

    .welcome-art {
      display: grid;
      width: 118px;
      height: 118px;
      flex: 0 0 118px;
      place-items: center;
      border: 1px solid rgba(255, 255, 255, 0.24);
      border-radius: 2rem;
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.07));
      color: #dbeafe;
      font-size: 3.1rem;
      transform: rotate(5deg);
    }

    .dashboard-section-heading {
      color: var(--app-text);
    }

    @media (max-width: 575.98px) {
      .welcome-art {
        display: none;
      }

      .navbar-custom .container {
        gap: 0.5rem;
      }

      .navbar-brand {
        font-size: 0.95rem;
      }
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
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
</head>

<body>

  <!-- Navbar Siswa -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-3 py-3 mb-4 shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white me-2" style="width: 38px; height: 38px;"><i class="bi bi-mortarboard-fill"></i></span> CBT Portal
      </a>
      <div class="d-flex align-items-center gap-3">
        <!-- Tombol Toggle Dark Mode -->
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema">
          <i data-theme-icon class="bi bi-moon-stars-fill"></i>
        </button>
        <div class="dropdown">
          <button class="student-account-toggle" type="button" id="studentAccountMenu" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Buka menu akun">
            <i class="bi bi-person-fill" aria-hidden="true"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end student-account-menu" aria-labelledby="studentAccountMenu">
            <div class="px-3 py-2">
              <div class="fw-bold text-body small"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
              <div class="text-muted small">@<?= htmlspecialchars($_SESSION['username']) ?></div>
            </div>
            <hr class="dropdown-divider my-2">
            <button type="button" class="dropdown-item rounded-2 text-danger" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout">
              <i class="bi bi-box-arrow-right me-2"></i>Keluar / Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="container pb-5">
    <section class="welcome-panel mb-4 mb-lg-5">
      <div class="d-flex align-items-center justify-content-between gap-4">
        <div>
          <span class="welcome-eyebrow"><i class="bi bi-stars"></i> RUANG BELAJAR DIGITAL</span>
          <h1 class="mb-2">Selamat datang kembali,<br><?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</h1>
          <p>Siap mengasah kemampuan hari ini? Pilih ujian yang tersedia, siapkan token dari pengawas, lalu kerjakan dengan tenang.</p>
          <span class="welcome-stat"><i class="bi bi-journal-check text-info"></i><?= $jumlah_ujian_aktif ?> paket ujian aktif tersedia</span>
        </div>
        <div class="welcome-art" aria-hidden="true"><i class="bi bi-mortarboard-fill"></i></div>
      </div>
    </section>

    <div class="mb-4">
      <h4 class="dashboard-section-heading fw-bold mb-1">Jelajahi Paket Ujian</h4>
      <p class="text-muted small mb-0">Pilih ujian yang dijadwalkan untuk Anda dan pastikan koneksi internet stabil.</p>
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
                  <a href="petunjuk.php?exam_id=<?= $u['id'] ?>" class="btn btn-primary w-100 rounded-3 fw-semibold">
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