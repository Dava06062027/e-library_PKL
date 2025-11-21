<!-- resources/views/penerbits/partials/edit-penerbit-modal.blade.php -->

<div class="modal fade" id="modalEditPenerbit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-penerbit" class="modal-content">
            @csrf
            <input type="hidden" id="edit-penerbit-id" name="id">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Penerbit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input id="edit-nama" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input id="edit-alamat" name="alamat" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">No Telepon</label>
                    <input id="edit-no-telepon" name="no_telepon" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input id="edit-email" name="email" type="email" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>