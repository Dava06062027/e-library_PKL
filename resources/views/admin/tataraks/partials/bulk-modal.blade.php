<div class="modal fade" id="modalBulkTatarak" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="form-bulk-tatarak" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bulk Penataan Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Button untuk tambah buku -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Buku & Eksemplar</label>
                    <button id="btn-add-buku" type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSelectBuku">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Buku</span>
                    </button>
                </div>

                <!-- Step 2: Daftar buku & eksemplar yang sudah dipilih -->
                <div id="selected-books-container" class="mb-4" style="display:none;">
                    <h6 class="fw-bold mb-3">Buku & Eksemplar Terpilih:</h6>
                    <div id="selected-books-list" class="border rounded p-3 bg-light">
                        <!-- Will be populated dynamically -->
                    </div>
                    <div class="mt-2 text-muted">
                        <small>Total Eksemplar: <span id="total-eksemplar-count">0</span></small>
                    </div>
                </div>

                <!-- Step 3: Pilih Rak -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Rak Tujuan</label>
                    <select name="id_rak" id="select-rak" class="form-select" required>
                        <option value="">-- Pilih Rak --</option>
                        @foreach(\App\Models\Rak::all() as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama }} (Kapasitas: {{ $rak->kapasitas }}, {{ $rak->kolom }}x{{ $rak->baris }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 4: Auto position option -->
                <div class="form-check mb-3">
                    <input type="checkbox" id="auto-position" class="form-check-input" checked>
                    <label class="form-check-label" for="auto-position">
                        Auto Generate Posisi (Sequential dari slot kosong pertama)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Submit Penataan</button>
            </div>
        </form>
    </div>
</div>
