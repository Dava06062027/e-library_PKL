<div class="modal fade" id="modalEditTatarak" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-tatarak" class="modal-content">
            @csrf
            <input type="hidden" id="edit-id" name="id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Penataan Buku
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Current Info Display -->
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Informasi Saat Ini</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Buku:</small>
                            <div id="current-buku-info" class="fw-semibold">-</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Rak:</small>
                            <div id="current-rak-info" class="fw-semibold">-</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Eksemplar Buku <span class="text-danger">*</span></label>
                        <select id="edit-id_buku_item" name="id_buku_item" class="form-select" required>
                            <option value="">-- Pilih Eksemplar --</option>
                            @foreach(\App\Models\BukuItem::with('buku')->get() as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->buku->judul }} - {{ $item->barcode }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Barcode eksemplar buku</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Rak <span class="text-danger">*</span></label>
                        <select id="edit-id_rak" name="id_rak" class="form-select" required>
                            <option value="">-- Pilih Rak --</option>
                            @foreach(\App\Models\Rak::with('lokasi')->get() as $rak)
                                <option value="{{ $rak->id }}">
                                    {{ $rak->nama }} - {{ $rak->lokasi->ruang ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Rak tujuan penataan</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kolom <span class="text-danger">*</span></label>
                        <input id="edit-kolom" name="kolom" class="form-control" required type="number" min="1" placeholder="Nomor kolom">
                        <div class="form-text">Posisi kolom pada rak</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Baris <span class="text-danger">*</span></label>
                        <input id="edit-baris" name="baris" class="form-control" required type="number" min="1" placeholder="Nomor baris">
                        <div class="form-text">Posisi baris pada rak</div>
                    </div>
                </div>

                <div class="alert alert-warning mb-0">
                    <small><i class="bi bi-exclamation-triangle me-2"></i>Perubahan eksemplar atau rak akan mempengaruhi data penataan sebelumnya.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
