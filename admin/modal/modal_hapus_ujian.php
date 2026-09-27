<!-- Modal Konfirmasi Hapus Ujian Modern -->
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4 bg-white">
      <div class="modal-body p-0">
        <!-- Ikon Peringatan Elegan -->
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background-color: #fef2f2; color: #dc2626; border: 4px solid #fee2e2;">
          <i class="bi bi-exclamation-triangle-fill fs-2"></i>
        </div>

        <h5 class="fw-bold text-dark mb-2" id="modalHapusLabel">Hapus Paket Ujian?</h5>
        <p class="text-muted small mb-2">
          Apakah Anda yakin ingin menghapus paket ujian ini?
        </p>

        <!-- Box Preview Nama Ujian yang Dipilih -->
        <div class="bg-light p-2 rounded-3 border mb-3">
          <strong class="text-dark small d-block" id="teksNamaUjian">-</strong>
        </div>

        <p class="text-danger small mb-4" style="font-size: 0.78rem;">
          <i class="bi bi-info-circle me-1"></i> Tindakan ini permanen. Seluruh bank soal dan rekap nilai siswa pada paket ini akan ikut terhapus.
        </p>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light rounded-3 w-50 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">
            Batal
          </button>
          <a href="#" id="btnEksekusiHapus" class="btn btn-danger rounded-3 w-50 py-2 fw-semibold shadow-sm">
            Ya, Hapus Data
          </a>
        </div>
      </div>
    </div>
  </div>
</div>