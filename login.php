<?php
session_start();
require_once 'config/database.php';
global $koneksi;

// Jika sudah login, langsung alihkan sesuai role
if (isset($_SESSION['login'])) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
  } else {
    header("Location: siswa/index.php");
  }
  exit;
}

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
  $password = md5(trim($_POST['password']));

  $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");

  if (mysqli_num_rows($query) === 1) {
    $user = mysqli_fetch_assoc($query);
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
      header("Location: admin/index.php");
    } else {
      header("Location: siswa/index.php");
    }
    exit;
  } else {
    $pesan_error = 'Username atau kata sandi tidak sesuai!';
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - CBT Online Modern</title>
  <script src="assets/js/dark-mode.js?v=2"></script>
  <!-- Google Fonts & Bootstrap 5 -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      color-scheme: light;
      --ink: #172033;
      --muted: #778198;
      --blue: #3563f6;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
      color: var(--ink);
      background:
        radial-gradient(ellipse at 14% 15%, rgba(91, 132, 255, .12), transparent 32%),
        radial-gradient(ellipse at 92% 88%, rgba(116, 83, 236, .10), transparent 30%),
        #f3f6fc;
    }

    .login-shell {
      width: min(100%, 1020px);
      min-height: 620px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, .85);
      border-radius: 28px;
      background: #fff;
      box-shadow: 0 30px 80px rgba(35, 52, 91, .14), 0 4px 14px rgba(35, 52, 91, .05);
    }

    .login-aside {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      padding: 42px;
      color: #fff;
      background: linear-gradient(145deg, #101b36 0%, #172b58 55%, #2449a0 100%);
    }

    .login-aside::before,
    .login-aside::after {
      position: absolute;
      content: "";
      border: 1px solid rgba(255, 255, 255, .12);
      border-radius: 50%;
      pointer-events: none;
    }

    .login-aside::before {
      width: 390px;
      height: 390px;
      right: -180px;
      top: 130px;
      box-shadow: 0 0 0 38px rgba(255, 255, 255, .025), 0 0 0 78px rgba(255, 255, 255, .025);
    }

    .login-aside::after {
      width: 220px;
      height: 220px;
      left: -145px;
      bottom: -95px;
      background: rgba(98, 137, 255, .16);
    }

    .brand,
    .aside-copy,
    .aside-footer {
      position: relative;
      z-index: 1;
    }

    .brand-mark {
      width: 44px;
      height: 44px;
      display: inline-grid;
      place-items: center;
      margin-right: 12px;
      border: 1px solid rgba(255, 255, 255, .22);
      border-radius: 14px;
      background: rgba(255, 255, 255, .12);
      color: #b9d0ff;
      font-size: 1.35rem;
    }

    .brand-name {
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .brand-caption {
      color: #aab9d8;
      font-size: .7rem;
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
      color: #c3d4ff;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .13em;
      text-transform: uppercase;
    }

    .eyebrow::before {
      width: 20px;
      height: 2px;
      content: "";
      background: #87aaff;
    }

    .aside-copy h1 {
      max-width: 390px;
      margin-bottom: 18px;
      font-size: clamp(2.1rem, 4vw, 3.2rem);
      font-weight: 700;
      letter-spacing: -.055em;
      line-height: 1.13;
    }

    .aside-copy>p {
      max-width: 370px;
      color: #b6c3de;
      font-size: .92rem;
      line-height: 1.8;
    }

    .feature-list {
      display: grid;
      gap: 14px;
      margin-top: 32px;
    }

    .feature {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #e0e9ff;
      font-size: .82rem;
    }

    .feature i {
      color: #8fb0ff;
      font-size: 1rem;
    }

    .aside-footer {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #aab9d8;
      font-size: .72rem;
    }

    .online-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #4ade80;
      box-shadow: 0 0 0 4px rgba(74, 222, 128, .13);
    }

    .login-panel {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 58px clamp(28px, 6vw, 72px);
    }

    .login-content {
      width: 100%;
      max-width: 370px;
    }

    .mobile-brand {
      display: none;
    }

    .login-content h2 {
      margin-bottom: 8px;
      color: var(--ink);
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: -.045em;
    }

    .login-subtitle {
      margin-bottom: 32px;
      color: var(--muted);
      font-size: .88rem;
      line-height: 1.6;
    }

    .form-label {
      margin-bottom: 8px;
      color: #39445a;
      font-size: .78rem;
      font-weight: 700;
    }

    .field-wrap {
      display: flex;
      align-items: center;
      min-height: 54px;
      border: 1px solid #e1e6f0;
      border-radius: 12px;
      background: #fff;
      transition: border-color .2s ease, box-shadow .2s ease;
    }

    .field-wrap:focus-within {
      border-color: #7b9bff;
      box-shadow: 0 0 0 4px rgba(53, 99, 246, .09);
    }

    .field-icon {
      width: 48px;
      flex: 0 0 48px;
      text-align: center;
      color: #8994a9;
    }

    .field-wrap .form-control {
      min-height: 52px;
      padding: 12px 10px 12px 0;
      border: 0;
      border-radius: 0 12px 12px 0;
      background: transparent;
      box-shadow: none;
      font-size: .87rem;
    }

    .field-wrap .form-control::placeholder {
      color: #a1a9b8;
    }

    .toggle-password {
      margin-right: 8px;
      border: 0;
      background: transparent;
      color: #7d879a;
    }

    .btn-primary-gradient {
      min-height: 54px;
      border: 0;
      border-radius: 12px;
      background: linear-gradient(110deg, #4778ff, #3155e7);
      box-shadow: 0 9px 20px rgba(53, 99, 246, .22);
      font-weight: 700;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .btn-primary-gradient:hover {
      transform: translateY(-2px);
      background: linear-gradient(110deg, #3d6df4, #294bd4);
      box-shadow: 0 12px 24px rgba(53, 99, 246, .3);
    }

    .credentials {
      margin-top: 26px;
      padding: 16px;
      border: 1px solid #e8edf6;
      border-radius: 13px;
      background: #f8faff;
    }

    .credentials-title {
      margin-bottom: 10px;
      color: #7b8598;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .credential-row {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      color: #515d72;
      font-size: .72rem;
    }

    .credential-row+.credential-row {
      margin-top: 7px;
    }

    .credential-row strong {
      color: #303c52;
      font-weight: 600;
    }

    @media (max-width: 767.98px) {
      body {
        padding: 18px 14px;
      }

      .login-shell {
        display: block;
        min-height: 0;
        max-width: 480px;
        border-radius: 22px;
      }

      .login-aside {
        display: none;
      }

      .login-panel {
        display: block;
        padding: 34px 28px 30px;
      }

      .mobile-brand {
        display: flex;
        align-items: center;
        margin-bottom: 34px;
      }

      .mobile-brand .brand-mark {
        border-color: #e0e9ff;
        background: #eff4ff;
        color: var(--blue);
      }

      .mobile-brand .brand-name {
        color: var(--ink);
      }

      .mobile-brand .brand-caption {
        color: #8b95a7;
      }

      .login-content h2 {
        font-size: 1.65rem;
      }

      .login-subtitle {
        margin-bottom: 26px;
      }
    }

    @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        transition-duration: .01ms !important;
      }
    }
  </style>
  <link rel="stylesheet" href="assets/css/theme.css?v=4">
</head>

<body>

  <button type="button" class="theme-toggle page-theme-toggle" data-theme-toggle aria-label="Ganti tema">
    <i data-theme-icon class="bi bi-moon-stars-fill"></i>
  </button>

  <main class="login-shell">
    <section class="login-aside" aria-label="Informasi CBT Online">
      <div class="brand d-flex align-items-center">
        <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
        <span><span class="brand-name d-block">CBT Online</span><span class="brand-caption">Learning & Assessment</span></span>
      </div>

      <div class="aside-copy">
        <span class="eyebrow">Portal akademik</span>
        <h1>Ujian lebih terarah, belajar lebih bermakna.</h1>
        <p>Kelola proses evaluasi dan ikuti ujian dengan mudah melalui satu portal yang aman dan praktis.</p>
        <div class="feature-list">
          <div class="feature"><i class="bi bi-shield-check"></i><span>Akses akun yang aman dan personal</span></div>
          <div class="feature"><i class="bi bi-clock-history"></i><span>Informasi ujian dan waktu secara real-time</span></div>
          <div class="feature"><i class="bi bi-graph-up-arrow"></i><span>Hasil evaluasi tersimpan terpusat</span></div>
        </div>
      </div>

      <div class="aside-footer"><span class="online-dot"></span> Sistem pembelajaran siap digunakan</div>
    </section>

    <section class="login-panel" aria-label="Form masuk">
      <div class="login-content">
        <div class="mobile-brand">
          <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
          <span><span class="brand-name d-block">CBT Online</span><span class="brand-caption">Learning & Assessment</span></span>
        </div>

        <h2>Selamat datang</h2>
        <p class="login-subtitle">Masuk ke akun Anda untuk melanjutkan ke portal ujian.</p>

        <?php if (!empty($pesan_error)): ?>
          <div class="alert alert-danger py-2 px-3 small rounded-3 d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
            <div><?= htmlspecialchars($pesan_error) ?></div>
          </div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="on">
          <div class="mb-3">
            <label class="form-label" for="username">Username / NISN</label>
            <div class="field-wrap">
              <span class="field-icon"><i class="bi bi-person"></i></span>
              <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" autocomplete="username" required autofocus>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label" for="password">Kata sandi</label>
            <div class="field-wrap">
              <span class="field-icon"><i class="bi bi-shield-lock"></i></span>
              <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
              <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="bi bi-eye"></i></button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary-gradient text-white w-100">
            Masuk ke Sistem <i class="bi bi-arrow-right ms-2"></i>
          </button>
        </form>

        <div class="credentials">
          <div class="credentials-title"><i class="bi bi-info-circle me-1"></i> Akun login pengujian</div>
          <div class="credential-row"><span>Administrator</span><strong>admin / admin123</strong></div>
          <div class="credential-row"><span>Siswa</span><strong>siswa01 / siswa123</strong></div>
        </div>
      </div>
    </section>
  </main>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const showing = passwordInput.type === 'password';
      passwordInput.type = showing ? 'text' : 'password';
      togglePassword.innerHTML = showing ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      togglePassword.setAttribute('aria-label', showing ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      togglePassword.setAttribute('aria-pressed', String(showing));
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>