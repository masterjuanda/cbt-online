<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

// Validasi Sesi Ujian
$sesi = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT se.*, e.judul_ujian, e.durasi_menit 
    FROM student_exams se 
    JOIN exams e ON se.exam_id = e.id 
    WHERE se.user_id = $user_id AND se.exam_id =$exam_id
"));

if (!$sesi || $sesi['status'] === 'selesai') {
  header("Location: index.php");
  exit;
}

// Hitung Sisa Waktu (Durasi Menit - Selisih Waktu Mulai)
$waktu_mulai_detik = strtotime($sesi['waktu_mulai']);
$waktu_sekarang_detik = time();
$durasi_total_detik = $sesi['durasi_menit'] * 60;
$detik_berjalan = $waktu_sekarang_detik - $waktu_mulai_detik;
$sisa_detik = $durasi_total_detik - $detik_berjalan;

// Jika sisa waktu sudah habis sebelum buka halaman
if ($sisa_detik <= 0) {
  $sisa_detik = 0;
}

// PROSES SUBMIT JAWABAN & AUTO-GRADING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ujian'])) {
  $answers = isset($_POST['jawaban']) ? $_POST['jawaban'] : [];
  $student_exam_id = $sesi['id'];

  // Ambil kunci jawaban asli
  $query_questions = mysqli_query($koneksi, "SELECT id, kunci_jawaban FROM questions WHERE exam_id = $exam_id");
  $total_soal = mysqli_num_rows($query_questions);
  $jumlah_benar = 0;

  while ($q = mysqli_fetch_assoc($query_questions)) {
    $qid = $q['id'];
    $jawaban_terpilih = isset($answers[$qid]) ? $answers[$qid] : null;

    // Simpan jawaban siswa ke student_answers
    if ($jawaban_terpilih !== null) {
      mysqli_query($koneksi, "INSERT INTO student_answers (student_exam_id, question_id, jawaban_siswa) VALUES ($student_exam_id, $qid, '$jawaban_terpilih')");
    }

    if ($jawaban_terpilih === $q['kunci_jawaban']) {
      $jumlah_benar++;
    }
  }

  // Hitung Nilai Akhir (Skala 0 - 100)
  $nilai_akhir = ($total_soal > 0) ? ($jumlah_benar / $total_soal) * 100 : 0;
  $waktu_selesai = date('Y-m-d H:i:s');

  // Update status ujian jadi selesai
  mysqli_query($koneksi, "UPDATE student_exams SET total_nilai = $nilai_akhir, waktu_selesai = '$waktu_selesai', status = 'selesai' WHERE id =$student_exam_id");

  header("Location: hasil.php?exam_id=" . $exam_id);
  exit;
}

// Ambil Daftar Soal
$soal_list = mysqli_query($koneksi, "SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lembar Ujian - <?= htmlspecialchars($sesi['judul_ujian']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
    }

    .sticky-top-timer {
      position: sticky;
      top: 1rem;
      z-index: 1020;
    }

    .timer-box {
      background: #0f172a;
      color: #ffffff;
      border-radius: 1rem;
      padding: 1rem 1.5rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }

    .question-card {
      border: none;
      border-radius: 1rem;
      background: #ffffff;
      margin-bottom: 1.5rem;
    }

    .option-item {
      border: 1.5px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 0.75rem 1rem;
      margin-bottom: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .option-item:hover {
      background-color: #f1f5f9;
      border-color: #cbd5e1;
    }

    .form-check-input:checked~.form-check-label {
      font-weight: 600;
      color: #2563eb;
    }

    @media (max-width: 767.98px) {
      .submit-action {
        text-align: center !important;
      }
    }
  </style>
</head>

<body>

  <div class="container py-4">
    <!-- Header & Timer Section -->
    <div class="sticky-top-timer mb-4">
      <div class="timer-box d-flex justify-content-between align-items-center">
        <div>
          <h5 class="fw-bold mb-0 text-white"><?= htmlspecialchars($sesi['judul_ujian']) ?></h5>
          <small class="text-secondary">Peserta: <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></small>
        </div>
        <div class="text-end">
          <small class="text-secondary d-block fw-semibold">SISA WAKTU</small>
          <span id="countdown" class="fs-4 fw-bold text-warning font-monospace">--:--:--</span>
        </div>
      </div>
    </div>

    <!-- Lembar Soal -->
    <form id="formUjian" action="" method="POST">
      <input type="hidden" name="submit_ujian" value="1">

      <div class="row justify-content-center">
        <div class="col-lg-10">
          <?php $no = 1;
          while ($s = mysqli_fetch_assoc($soal_list)): ?>
            <div class="card question-card shadow-sm p-4">
              <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold me-2">Nomor <?= $no++ ?></span>
              </div>
              <p class="fw-semibold text-dark fs-5 mb-4" style="white-space: pre-line;"><?= htmlspecialchars($s['pertanyaan']) ?></p>

              <div class="options-container">
                <div class="form-check option-item">
                  <input class="form-check-input" type="radio" name="jawaban[<?= $s['id'] ?>]" id="opt_a_<?= $s['id'] ?>" value="A">
                  <label class="form-check-label w-100" for="opt_a_<?= $s['id'] ?>">
                    <strong>A.</strong> <?= htmlspecialchars($s['opsi_a']) ?>
                  </label>
                </div>
                <div class="form-check option-item">
                  <input class="form-check-input" type="radio" name="jawaban[<?= $s['id'] ?>]" id="opt_b_<?= $s['id'] ?>" value="B">
                  <label class="form-check-label w-100" for="opt_b_<?= $s['id'] ?>">
                    <strong>B.</strong> <?= htmlspecialchars($s['opsi_b']) ?>
                  </label>
                </div>
                <div class="form-check option-item">
                  <input class="form-check-input" type="radio" name="jawaban[<?= $s['id'] ?>]" id="opt_c_<?= $s['id'] ?>" value="C">
                  <label class="form-check-label w-100" for="opt_c_<?= $s['id'] ?>">
                    <strong>C.</strong> <?= htmlspecialchars($s['opsi_c']) ?>
                  </label>
                </div>
                <div class="form-check option-item">
                  <input class="form-check-input" type="radio" name="jawaban[<?= $s['id'] ?>]" id="opt_d_<?= $s['id'] ?>" value="D">
                  <label class="form-check-label w-100" for="opt_d_<?= $s['id'] ?>">
                    <strong>D.</strong> <?= htmlspecialchars($s['opsi_d']) ?>
                  </label>
                </div>
                <div class="form-check option-item">
                  <input class="form-check-input" type="radio" name="jawaban[<?= $s['id'] ?>]" id="opt_e_<?= $s['id'] ?>" value="E">
                  <label class="form-check-label w-100" for="opt_e_<?= $s['id'] ?>">
                    <strong>E.</strong> <?= htmlspecialchars($s['opsi_e']) ?>
                  </label>
                </div>
              </div>
            </div>
          <?php endwhile; ?>

          <div class="submit-action text-end mt-4 mb-5">
            <button type="button" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiSelesai">
              <i class="bi bi-check2-circle me-1"></i> Selesaikan Ujian
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Modal Konfirmasi Selesaikan Ujian -->
  <div class="modal fade" id="modalKonfirmasiSelesai" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
      <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4 bg-white">
        <div class="modal-body p-0">
          <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background-color: #ecfdf5; color: #059669; border: 4px solid #d1fae5;">
            <i class="bi bi-send-check-fill fs-2"></i>
          </div>
          <h5 class="fw-bold text-dark mb-2" id="modalSelesaiLabel">Selesaikan Ujian?</h5>
          <p class="text-muted small mb-4">
            Apakah Anda yakin ingin mengumpulkan jawaban? Setelah dikumpulkan, jawaban tidak dapat diubah kembali.
          </p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light rounded-3 w-50 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">Periksa Lagi</button>
            <button type="button" id="btnSubmitConfirmed" class="btn btn-success rounded-3 w-50 py-2 fw-semibold shadow-sm">
              Ya, Kumpulkan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Countdown Script -->
  <script>
    let sisaDetik = <?= (int)$sisa_detik ?>;
    const timerEl = document.getElementById('countdown');
    const formUjian = document.getElementById('formUjian');

    function formatWaktu(totalDetik) {
      let jam = Math.floor(totalDetik / 3600);
      let menit = Math.floor((totalDetik % 3600) / 60);
      let detik = totalDetik % 60;

      return `${jam.toString().padStart(2, '0')}:${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;
    }

    const intervalTimer = setInterval(() => {
      if (sisaDetik <= 0) {
        clearInterval(intervalTimer);
        timerEl.innerText = "00:00:00";
        alert("Waktu ujian telah berakhir! Lembar jawaban Anda akan otomatis dikumpulkan.");
        formUjian.submit();
      } else {
        timerEl.innerText = formatWaktu(sisaDetik);
        sisaDetik--;
      }
    }, 1000);

    document.getElementById('btnSubmitConfirmed').addEventListener('click', () => formUjian.submit());
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>