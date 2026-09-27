<!-- admin/modal_logout.php -->
<div class="modal fade" id="modalKonfirmasiLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4 bg-white">
      <div class="modal-body p-0">
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background-color: #fee2e2; color: #ef4444;">
          <i class="bi bi-box-arrow-right fs-1"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2" id="modalLogoutLabel">Konfirmasi Keluar</h5>
        <p class="text-muted small mb-4">
          Apakah Anda yakin ingin mengakhiri sesi administrator ini? Anda harus masuk kembali untuk mengelola ujian.
        </p>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light rounded-3 w-50 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
          <a href="../logout.php" class="btn btn-danger rounded-3 w-50 py-2 fw-semibold shadow-sm">Ya, Keluar</a>
        </div>
      </div>
    </div>
  </div>
</div>