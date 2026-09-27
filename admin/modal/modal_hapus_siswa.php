<!-- Modal Konfirmasi Hapus Siswa Modern -->
<div class="modal fade" id="modalHapusSiswa" tabindex="-1" aria-labelledby="modalHapusSiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4 bg-white">
      <div class="modal-body p-0">
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background-color: #fef2f2; color: #dc2626; border: 4px solid #fee2e2;">
          <i class="bi bi-person-x-fill fs-2"></i>
        </div>

        <h5 class="fw-bold text-dark mb-2" id="modalHapusSiswaLabel">Hapus Akun Siswa?</h5>
        <p class="text-muted small mb-2">
          Apakah Anda yakin ingin menghapus akun peserta berikut?
        </p>

        <div class="bg-light p-3 rounded-3 border mb-3 text-start">
          <div class="small text-muted">Nama Peserta:</div>
          <strong class="text-dark d-block mb-1" id="teksNamaSiswa">-</strong>
          <div class="small text-muted">NISN / Username: <span class="font-monospace text-dark fw-semibold" id="teksUsernameSiswa">-</span></div>
        </div>

        <p class="text-danger small mb-4" style="font-size: 0.78rem;">
          <i class="bi bi-info-circle me-1"></i> Data nilai ujian siswa ini juga akan ikut terhapus dari sistem.
        </p>

        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light rounded-3 w-50 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
          <a href="#" id="btnEksekusiHapusSiswa" class="btn btn-danger rounded-3 w-50 py-2 fw-semibold shadow-sm">Ya, Hapus</a>
        </div>
      </div>
    </div>
  </div>
</div>