<?php
session_start();
require_once '../config/database.php';
global $koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$pesan = '';
$pesan_error = '';

// 1. TAMBAH SISWA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
  $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
  $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
  $password     = md5(trim($_POST['password']));

  $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
  if (mysqli_num_rows($cek) > 0) {
    $pesan_error = 'Username / NISN sudah terdaftar!';
  } else {
    $insert = mysqli_query($koneksi, "INSERT INTO users (username, nama_lengkap, password, role) VALUES ('$username', '$nama_lengkap', '$password', 'siswa')");
    if ($insert) {
      $pesan = 'Siswa baru berhasil ditambahkan!';
    }
  }
}

// 2. EDIT / UPDATE SISWA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_siswa'])) {
  $id_siswa     = (int)$_POST['id_siswa'];
  $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
  $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
  $password     = trim($_POST['password']);

  // Cek apakah username sudah dipakai orang lain
  $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username' AND id != $id_siswa");
  if (mysqli_num_rows($cek) > 0) {
    $pesan_error = 'Username / NISN sudah digunakan oleh siswa lain!';
  } else {
    if (!empty($password)) {
      // Jika kata sandi diisi baru
      $pass_hash = md5($password);
      $update = mysqli_query($koneksi, "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap', password = '$pass_hash' WHERE id = $id_siswa AND role = 'siswa'");
    } else {
      // Jika kata sandi dibiarkan kosong (tidak diubah)
      $update = mysqli_query($koneksi, "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap' WHERE id = $id_siswa AND role = 'siswa'");
    }

    if ($update) {
      $pesan = 'Data siswa berhasil diperbarui!';
    }
  }
}

// 3. HAPUS SISWA
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  mysqli_query($koneksi, "DELETE FROM users WHERE id = $id AND role = 'siswa'");
  header("Location: siswa.php");
  exit;
}

$list_siswa = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'siswa' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Siswa - CBT Online</title>
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
  </style>
  <link rel="stylesheet" href="../assets/css/admin-responsive.css?v=2">
</head>

<body>

  <div class="d-flex">
    <?php include 'partials/navigation.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="min-height: 100vh;">
      <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom admin-page-titlebar">
        <div>
          <h4 class="fw-bold mb-0">Manajemen Data Siswa</h4>
          <p class="text-muted small mb-0">Kelola akun dan informasi peserta ujian evaluasi</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
          <i class="bi bi-person-plus me-1"></i> Tambah Siswa Baru
        </button>
      </div>

      <?php if (!empty($pesan)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($pesan) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($pesan_error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light border-bottom">
              <tr class="small text-secondary">
                <th class="ps-4">NO</th>
                <th>NISN / USERNAME</th>
                <th>NAMA LENGKAP</th>
                <th>TANGGAL DIBUAT</th>
                <th class="text-end pe-4">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($list_siswa) > 0): ?>
                <?php $no = 1;
                while ($s = mysqli_fetch_assoc($list_siswa)): ?>
                  <tr>
                    <td class="ps-4 text-muted small"><?= $no++ ?></td>
                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace"><?= htmlspecialchars($s['username']) ?></span></td>
                    <td class="fw-semibold text-dark"><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                    <td><small class="text-muted"><?= date('d M Y', strtotime($s['created_at'])) ?></small></td>
                    <td class="text-end pe-4">
                      <!-- Tombol Edit Siswa -->
                      <button type="button"
                        class="btn btn-sm btn-outline-warning rounded-2 me-1 btn-edit-siswa"
                        data-id="<?= $s['id'] ?>"
                        data-username="<?= htmlspecialchars($s['username']) ?>"
                        data-nama="<?= htmlspecialchars($s['nama_lengkap']) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditSiswa"
                        aria-label="Edit data siswa"
                        title="Ubah Data Siswa">
                        <i class="bi bi-pencil-square"></i>
                      </button>

                      <!-- Tombol Hapus Siswa -->
                      <button type="button"
                        class="btn btn-sm btn-outline-danger rounded-2 btn-hapus-siswa"
                        data-id="<?= $s['id'] ?>"
                        data-nama="<?= htmlspecialchars($s['nama_lengkap']) ?>"
                        data-username="<?= htmlspecialchars($s['username']) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalHapusSiswa"
                        aria-label="Hapus siswa"
                        title="Hapus Siswa">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted small">Belum ada data siswa terdaftar.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Siswa -->
  <div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="fw-bold">Tambah Akun Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">NISN / Username Login</label>
              <input type="text" name="username" class="form-control rounded-3" placeholder="Contoh: siswa02" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Nama Lengkap Siswa</label>
              <input type="text" name="nama_lengkap" class="form-control rounded-3" placeholder="Contoh: Siti Rahmawati" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Kata Sandi Default</label>
              <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah_siswa" class="btn btn-primary rounded-3 px-4">Simpan Siswa</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Siswa Modern -->
  <div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="fw-bold">Ubah Informasi Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <input type="hidden" name="id_siswa" id="editIdSiswa">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">NISN / Username Login</label>
              <input type="text" name="username" id="editUsername" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Nama Lengkap Siswa</label>
              <input type="text" name="nama_lengkap" id="editNamaLengkap" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru</label>
              <input type="password" name="password" class="form-control rounded-3" placeholder="Biarkan kosong jika tidak ingin mengubah sandi">
              <small class="text-muted" style="font-size: 0.75rem;">*Kosongkan kolom ini jika password tidak ingin diganti.</small>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="edit_siswa" class="btn btn-warning text-dark fw-semibold rounded-3 px-4">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>



  <?php include 'modal/modal_logout.php'; ?>
  <?php include 'modal/modal_hapus_siswa.php'; ?>

  <script>
    // Handler Tombol Edit
    document.querySelectorAll('.btn-edit-siswa').forEach(button => {
      button.addEventListener('click', function() {
        document.getElementById('editIdSiswa').value = this.getAttribute('data-id');
        document.getElementById('editUsername').value = this.getAttribute('data-username');
        document.getElementById('editNamaLengkap').value = this.getAttribute('data-nama');
      });
    });

    // Handler Tombol Hapus
    document.querySelectorAll('.btn-hapus-siswa').forEach(button => {
      button.addEventListener('click', function() {
        const idSiswa = this.getAttribute('data-id');
        const namaSiswa = this.getAttribute('data-nama');
        const usernameSiswa = this.getAttribute('data-username');

        document.getElementById('teksNamaSiswa').innerText = namaSiswa;
        document.getElementById('teksUsernameSiswa').innerText = usernameSiswa;
        document.getElementById('btnEksekusiHapusSiswa').setAttribute('href', 'siswa.php?hapus=' + idSiswa);
      });
    });
  </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>