<div class="modal fade" id="modalDetailTatarak" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>Detail Penataan Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Informasi Buku -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-book me-2"></i>Informasi Buku</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="text-muted small">Judul Buku</label>
                                    <div class="fw-semibold" id="detail-judul-buku">-</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Barcode Eksemplar</label>
                                    <div class="fw-semibold" id="detail-barcode">
                                        <span class="badge bg-secondary">-</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Pengarang</label>
                                    <div id="detail-pengarang">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="text-muted small">Kondisi</label>
                                        <div id="detail-kondisi">-</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Status</label>
                                        <div id="detail-status">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Rak -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Informasi Rak</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="text-muted small">Nama Rak</label>
                                    <div class="fw-semibold" id="detail-nama-rak">
                                        <span class="badge bg-primary">-</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Lokasi</label>
                                    <div id="detail-lokasi">-</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Posisi</label>
                                    <div>
                                        <span class="badge bg-info" id="detail-posisi">-</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="text-muted small">Kapasitas Rak</label>
                                        <div id="detail-kapasitas">-</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Ukuran</label>
                                        <div id="detail-ukuran">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Petugas -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Informasi Petugas & Waktu</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Petugas</label>
                                        <div class="fw-semibold" id="detail-petugas">-</div>
                                        <small class="text-muted" id="detail-role">-</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Dibuat Pada</label>
                                        <div id="detail-created-at">-</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="text-muted small">Terakhir Diupdate</label>
                                        <div id="detail-updated-at">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
