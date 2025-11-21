<div class="modal fade" id="modalNewItem" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-new-item" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bookshelf me-2"></i>Create New Buku Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Buku <span class="text-danger">*</span></label>
                        <select name="id_buku" class="form-select" required>
                            <option value="">Select Buku</option>
                            @foreach($bukus as $buku)
                                <option value="{{ $buku->id }}">{{ $buku->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Removed barcode input, assuming trigger generates it -->
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select" required>
                            <option value="Baik">Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Reparasi">Reparasi</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sumber <span class="text-danger">*</span></label>
                        <select name="sumber" class="form-select" required>
                            <option value="Hibah">Hibah</option>
                            <option value="Beli">Beli</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rak</label>
                    <select name="id_rak" class="form-select">
                        <option value="">None</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
