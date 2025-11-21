<!-- resources/views/raks/partials/rak-detail-modal.blade.php -->

<div class="modal fade" id="modalRakDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detail Rak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <p id="detail-nama" class="mb-0"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Barcode</label>
                        <p id="detail-barcode" class="mb-0"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Kolom</label>
                        <p id="detail-kolom" class="mb-0"></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Baris</label>
                        <p id="detail-baris" class="mb-0"></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Kapasitas</label>
                        <p id="detail-kapasitas" class="mb-0"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Lokasi</label>
                        <p id="detail-lokasi" class="mb-0"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <p id="detail-kategori" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
