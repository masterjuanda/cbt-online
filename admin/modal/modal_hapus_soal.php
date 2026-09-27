<!-- Modal Konfirmasi Hapus Soal Modern -->
<div class="modal fade" id="modalHapusSoal" tabindex="-1" aria-labelledby="modalHapusSoalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4 bg-white">
      <div class="modal-body p-0">
        <!-- Ikon Peringatan Elegan -->
        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background-color: #fef2f2; color: #dc2626; border: 4px solid #fee2e2;">
          <i class="bi bi-trash3-fill fs-2"></i>
        </div>

        <h5 class="fw-bold text-dark mb-2" id="modalHapusSoalLabel">Hapus Butir Soal?</h5>
        <p class="text-muted small mb-3">
          Apakah Anda yakin ingin menghapus butir soal evaluasi berikut?
        </p>

        <!-- Cuplikan Soal yang Dipilih -->
        <div class="bg-light p-3 rounded-3 border text-start mb-4">
          <span class="badge bg-secondary-subtle text-secondary mb-1" id="labelNomorSoal">Soal #</span>
          <p class="text-dark small mb-0 fw-semibold fst-italic" id="teksCuplikanSoal">"..."</p>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light rounded-3 w-50 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">
            Batal
          </button>
          <a href="#" id="btnEksekusiHapusSoal" class="btn btn-danger rounded-3 w-50 py-2 fw-semibold shadow-sm">
            Ya, Hapus
          </a>
        </div>
      </div>
    </div>
  </div>
</div>