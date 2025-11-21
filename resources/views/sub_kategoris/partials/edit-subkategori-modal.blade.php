<!-- resources/views/sub_kategoris/partials/edit-subkategori-modal.blade.php - Update to 'nama' -->

<div class="modal fade" id="modalEditSubkategori" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-subkategori" class="modal-content">
            @csrf
            <input type="hidden" id="edit-subkategori-id" name="id">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Sub Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input id="edit-nama" name="nama" class="form-control" required>
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
