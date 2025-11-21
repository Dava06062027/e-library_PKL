<!-- resources/views/raks/partials/edit-rak-modal.blade.php -->

<div class="modal fade" id="modalEditRak" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-rak" class="modal-content">
            @csrf
            <input type="hidden" id="edit-rak-id" name="id">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Rak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input id="edit-nama" name="nama" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barcode <span class="text-danger">*</span></label>
                        <input id="edit-barcode" name="barcode" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kolom <span class="text-danger">*</span></label>
                        <input id="edit-kolom" name="kolom" type="number" min="1" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Baris <span class="text-danger">*</span></label>
                        <input id="edit-baris" name="baris" type="number" min="1" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input id="edit-kapasitas" name="kapasitas" type="number" min="1" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                        <select id="edit-id-lokasi" name="id_lokasi" class="form-select" required>
                            <option value="">Select Lokasi</option>
                            @foreach($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->ruang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select id="edit-id-kategori" name="id_kategori" class="form-select" required>
                            <option value="">Select Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
