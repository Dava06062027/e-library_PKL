<!-- resources/views/penerbits/partials/new-penerbit-modal.blade.php -->

<div class="modal fade" id="modalNewPenerbit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-new-penerbit" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Create New Penerbit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input name="alamat" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">No Telepon</label>
                    <input name="no_telepon" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>