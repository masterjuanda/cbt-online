<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$pesan = '';

$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$list_exams = mysqli_query($koneksi, "SELECT * FROM exams ORDER BY id DESC");

if ($exam_id == 0) {
  $first_exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM exams ORDER BY id DESC LIMIT 1"));
  if ($first_exam) {
    $exam_id = $first_exam['id'];
  }
}

// 1. TAMBAH SOAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_soal'])) {
  $selected_exam = (int)$_POST['exam_id'];
  $pertanyaan    = mysqli_real_escape_string($koneksi, trim($_POST['pertanyaan']));
  $opsi_a        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_a']));
  $opsi_b        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_b']));
  $opsi_c        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_c']));
  $opsi_d        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_d']));
  $opsi_e        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_e']));
  $kunci_jawaban = mysqli_real_escape_string($koneksi, trim($_POST['kunci_jawaban']));

  $query = mysqli_query($koneksi, "INSERT INTO questions (exam_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban) 
                                     VALUES ($selected_exam, '$pertanyaan', '$opsi_a', '$opsi_b', '$opsi_c', '$opsi_d', '$opsi_e', '$kunci_jawaban')");
  if ($query) {
    $pesan = 'Butir soal berhasil ditambahkan!';
    $exam_id = $selected_exam;
  }
}

// 2. EDIT / UPDATE SOAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_soal'])) {
  $id_soal       = (int)$_POST['id_soal'];
  $pertanyaan    = mysqli_real_escape_string($koneksi, trim($_POST['pertanyaan']));
  $opsi_a        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_a']));
  $opsi_b        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_b']));
  $opsi_c        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_c']));
  $opsi_d        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_d']));
  $opsi_e        = mysqli_real_escape_string($koneksi, trim($_POST['opsi_e']));
  $kunci_jawaban = mysqli_real_escape_string($koneksi, trim($_POST['kunci_jawaban']));

  $update = mysqli_query($koneksi, "UPDATE questions SET 
        pertanyaan = '$pertanyaan',
        opsi_a = '$opsi_a',
        opsi_b = '$opsi_b',
        opsi_c = '$opsi_c',
        opsi_d = '$opsi_d',
        opsi_e = '$opsi_e',
        kunci_jawaban = '$kunci_jawaban'
        WHERE id = $id_soal
    ");

  if ($update) {
    $pesan = 'Butir soal berhasil diperbarui!';
  }
}

// 3. HAPUS SOAL
if (isset($_GET['hapus'])) {
  $id_soal = (int)$_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM questions WHERE id = $id_soal");
  header("Location: soal.php?exam_id=" . $exam_id);
  exit;
}

$detail_exam = null;
$list_soal = null;
if ($exam_id > 0) {
  $detail_exam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM exams WHERE id = $exam_id"));
  $list_soal   = mysqli_query($koneksi, "SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY id ASC");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bank Soal - CBT Online</title>
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

    .option-box {
      background-color: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 0.5rem;
      padding: 0.5rem 0.75rem;
      margin-bottom: 0.35rem;
      font-size: 0.9rem;
    }

    .option-box.correct {
      background-color: #f0fdf4;
      border-color: #86efac;
      font-weight: 600;
      color: #15803d;
    }
  </style>
  <link rel="stylesheet" href="../assets/css/admin-responsive.css?v=5">
  <link rel="stylesheet" href="../assets/css/theme.css?v=4">
</head>

<body>

  <div class="d-flex">
    <?php include 'partials/navigation.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="min-height: 100vh;">
      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom admin-page-titlebar">
        <div>
          <h4 class="fw-bold mb-0">Manajemen Bank Soal</h4>
          <p class="text-muted small mb-0">Kelola butir pertanyaan dan kunci evaluasi pilihan ganda</p>
        </div>
        <div class="d-flex gap-2">
          <?php if ($detail_exam): ?>
            <a href="cetak_soal.php?exam_id=<?= $exam_id ?>" target="_blank" class="btn btn-outline-secondary rounded-3 shadow-sm">
              <i class="bi bi-printer me-1"></i> Cetak / PDF
            </a>
            <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
              <i class="bi bi-plus-circle me-1"></i> Tambah Soal
            </button>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($pesan)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($pesan) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Filter Paket Ujian -->
      <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
        <form action="" method="GET" class="row g-2 align-items-center">
          <div class="col-auto">
            <label class="small fw-bold text-secondary">Pilih Paket Ujian:</label>
          </div>
          <div class="col-md-6 col-lg-5">
            <select name="exam_id" class="form-select rounded-3 admin-mobile-select" aria-label="Pilih paket ujian" onchange="this.form.submit()">
              <?php
              mysqli_data_seek($list_exams, 0);
              while ($e = mysqli_fetch_assoc($list_exams)): ?>
                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $exam_id) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['judul_ujian']) ?> (Token: <?= $e['token'] ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </form>
      </div>

      <!-- Daftar Soal -->
      <?php if ($detail_exam): ?>
        <?php if (mysqli_num_rows($list_soal) > 0): ?>
          <?php $no = 1;
          while ($s = mysqli_fetch_assoc($list_soal)): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-3 p-4 bg-white">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                  Soal Nomor <?= $no++ ?>
                </span>
                <div class="d-flex gap-1">
                  <!-- Tombol Edit Soal -->
                  <button type="button"
                    class="action-icon-btn action-icon-btn--edit btn-edit-soal"
                    data-id="<?= $s['id'] ?>"
                    data-pertanyaan="<?= htmlspecialchars($s['pertanyaan']) ?>"
                    data-a="<?= htmlspecialchars($s['opsi_a']) ?>"
                    data-b="<?= htmlspecialchars($s['opsi_b']) ?>"
                    data-c="<?= htmlspecialchars($s['opsi_c']) ?>"
                    data-d="<?= htmlspecialchars($s['opsi_d']) ?>"
                    data-e="<?= htmlspecialchars($s['opsi_e']) ?>"
                    data-kunci="<?= $s['kunci_jawaban'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditSoal"
                    aria-label="Edit pertanyaan"
                    title="Ubah Soal">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <!-- Tombol Hapus Soal -->
                  <button type="button"
                    class="action-icon-btn action-icon-btn--delete btn-hapus-soal"
                    data-id="<?= $s['id'] ?>"
                    data-nomor="<?= $no - 1 ?>"
                    data-pertanyaan="<?= htmlspecialchars(mb_strimwidth($s['pertanyaan'], 0, 70, '...')) ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalHapusSoal"
                    aria-label="Hapus pertanyaan"
                    title="Hapus Soal">
                    <i class="bi bi-trash3"></i>
                  </button>
                </div>
              </div>

              <p class="fw-semibold text-dark mb-3" style="font-size: 1.05rem; white-space: pre-line;"><?= htmlspecialchars($s['pertanyaan']) ?></p>

              <div class="row g-2">
                <div class="col-md-6">
                  <div class="option-box <?= ($s['kunci_jawaban'] === 'A') ? 'correct' : '' ?>">
                    <strong>A.</strong> <?= htmlspecialchars($s['opsi_a']) ?>
                    <?= ($s['kunci_jawaban'] === 'A') ? '<i class="bi bi-check-circle-fill ms-2"></i> (Kunci)' : '' ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="option-box <?= ($s['kunci_jawaban'] === 'B') ? 'correct' : '' ?>">
                    <strong>B.</strong> <?= htmlspecialchars($s['opsi_b']) ?>
                    <?= ($s['kunci_jawaban'] === 'B') ? '<i class="bi bi-check-circle-fill ms-2"></i> (Kunci)' : '' ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="option-box <?= ($s['kunci_jawaban'] === 'C') ? 'correct' : '' ?>">
                    <strong>C.</strong> <?= htmlspecialchars($s['opsi_c']) ?>
                    <?= ($s['kunci_jawaban'] === 'C') ? '<i class="bi bi-check-circle-fill ms-2"></i> (Kunci)' : '' ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="option-box <?= ($s['kunci_jawaban'] === 'D') ? 'correct' : '' ?>">
                    <strong>D.</strong> <?= htmlspecialchars($s['opsi_d']) ?>
                    <?= ($s['kunci_jawaban'] === 'D') ? '<i class="bi bi-check-circle-fill ms-2"></i> (Kunci)' : '' ?>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="option-box <?= ($s['kunci_jawaban'] === 'E') ? 'correct' : '' ?>">
                    <strong>E.</strong> <?= htmlspecialchars($s['opsi_e']) ?>
                    <?= ($s['kunci_jawaban'] === 'E') ? '<i class="bi bi-check-circle-fill ms-2"></i> (Kunci)' : '' ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <i class="bi bi-folder-x text-muted display-4 mb-2"></i>
            <h6 class="fw-bold">Belum Ada Soal di Paket Ini</h6>
            <p class="text-muted small mb-0">Tekan tombol Tambah Soal untuk memasukkan butir pertanyaan evaluasi.</p>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal Tambah Soal -->
  <div class="modal fade" id="modalTambahSoal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom-0">
          <h5 class="fw-bold">Tambah Butir Soal Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
          <div class="modal-body py-0">
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Teks Pertanyaan</label>
              <textarea name="pertanyaan" rows="3" class="form-control rounded-3" placeholder="Tuliskan teks pertanyaan di sini..." required></textarea>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi A</label>
                <input type="text" name="opsi_a" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi B</label>
                <input type="text" name="opsi_b" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi C</label>
                <input type="text" name="opsi_c" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi D</label>
                <input type="text" name="opsi_d" class="form-control rounded-3" required>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Opsi E</label>
                <input type="text" name="opsi_e" class="form-control rounded-3" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold text-dark">Kunci Jawaban yang Benar</label>
              <select name="kunci_jawaban" class="form-select rounded-3 border-primary" required>
                <option value="A">Pilihan A</option>
                <option value="B">Pilihan B</option>
                <option value="C">Pilihan C</option>
                <option value="D">Pilihan D</option>
                <option value="E">Pilihan E</option>
              </select>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_soal" class="btn btn-primary rounded-3 px-4">Simpan Soal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Soal Modern -->
  <div class="modal fade" id="modalEditSoal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom-0">
          <h5 class="fw-bold">Ubah Butir Soal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <input type="hidden" name="id_soal" id="editIdSoal">
          <div class="modal-body py-0">
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Teks Pertanyaan</label>
              <textarea name="pertanyaan" id="editPertanyaan" rows="3" class="form-control rounded-3" required></textarea>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi A</label>
                <input type="text" name="opsi_a" id="editOpsiA" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi B</label>
                <input type="text" name="opsi_b" id="editOpsiB" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi C</label>
                <input type="text" name="opsi_c" id="editOpsiC" class="form-control rounded-3" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Opsi D</label>
                <input type="text" name="opsi_d" id="editOpsiD" class="form-control rounded-3" required>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Opsi E</label>
                <input type="text" name="opsi_e" id="editOpsiE" class="form-control rounded-3" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold text-dark">Kunci Jawaban yang Benar</label>
              <select name="kunci_jawaban" id="editKunci" class="form-select rounded-3 border-warning" required>
                <option value="A">Pilihan A</option>
                <option value="B">Pilihan B</option>
                <option value="C">Pilihan C</option>
                <option value="D">Pilihan D</option>
                <option value="E">Pilihan E</option>
              </select>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="edit_soal" class="btn btn-warning text-dark fw-semibold rounded-3 px-4">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <?php include 'modal/modal_logout.php'; ?>
  <?php include 'modal/modal_hapus_soal.php'; ?>

  <script>
    // Handler Tombol Edit Soal
    document.querySelectorAll('.btn-edit-soal').forEach(button => {
      button.addEventListener('click', function() {
        document.getElementById('editIdSoal').value = this.getAttribute('data-id');
        document.getElementById('editPertanyaan').value = this.getAttribute('data-pertanyaan');
        document.getElementById('editOpsiA').value = this.getAttribute('data-a');
        document.getElementById('editOpsiB').value = this.getAttribute('data-b');
        document.getElementById('editOpsiC').value = this.getAttribute('data-c');
        document.getElementById('editOpsiD').value = this.getAttribute('data-d');
        document.getElementById('editOpsiE').value = this.getAttribute('data-e');
        document.getElementById('editKunci').value = this.getAttribute('data-kunci');
      });
    });

    // Handler Tombol Hapus Soal
    document.querySelectorAll('.btn-hapus-soal').forEach(button => {
      button.addEventListener('click', function() {
        const idSoal = this.getAttribute('data-id');
        const nomorSoal = this.getAttribute('data-nomor');
        const cuplikanTeks = this.getAttribute('data-pertanyaan');
        const examId = "<?= $exam_id ?>";

        document.getElementById('labelNomorSoal').innerText = 'Soal Nomor ' + nomorSoal;
        document.getElementById('teksCuplikanSoal').innerText = '"' + cuplikanTeks + '"';
        document.getElementById('btnEksekusiHapusSoal').setAttribute('href', 'soal.php?exam_id=' + examId + '&hapus=' + idSoal);
      });
    });
  </script>

  <script src="../assets/js/admin-mobile-select.js?v=2"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>