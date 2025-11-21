<!-- resources/views/kategoris/partials/new-kategori-modal.blade.php -->

<div class="modal fade" id="modalNewKategori" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-new-kategori" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-tag me-2"></i>Create New Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input name="nama" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>