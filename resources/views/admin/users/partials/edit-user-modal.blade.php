<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-user" class="modal-content" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit-user-id" name="id">

            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h6 class="mb-3"><i class="bi bi-person-circle me-2"></i>Data Pribadi</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input id="edit-name" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input id="edit-email" name="email" type="email" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input id="edit-phone" name="phone" type="text" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input id="edit-birth-date" name="birth_date" type="date" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea id="edit-address" name="address" class="form-control" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password Baru (kosongkan jika tidak ingin ubah)</label>
                        <input id="edit-password" name="password" type="password" class="form-control" placeholder="Min. 8 karakter">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input id="edit-password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3"><i class="bi bi-card-text me-2"></i>Data Kependudukan</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIK (16 Digit)</label>
                        <input id="edit-nik" name="nik" type="text" class="form-control" placeholder="3374012345678901" maxlength="16">
                        <small class="text-muted">Sesuai KTP</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="edit-role" name="role" class="form-select" required>
                            <option value="Member">Member</option>
                            <option value="Officer">Officer</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Update Foto KTP (kosongkan jika tidak ingin ubah)</label>
                    <input id="edit-ktp-photo" name="ktp_photo" type="file" class="form-control" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                    <div id="current-ktp-preview" class="mt-2" style="display: none;">
                        <small class="text-muted d-block mb-1">Foto KTP saat ini:</small>
                        <img id="current-ktp-img" src="" alt="Current KTP" style="max-width: 200px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Update Foto Profil (kosongkan jika tidak ingin ubah)</label>
                    <input id="edit-photo" name="photo" type="file" class="form-control" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                    <div id="current-photo-preview" class="mt-2" style="display: none;">
                        <small class="text-muted d-block mb-1">Foto profil saat ini:</small>
                        <img id="current-photo-img" src="" alt="Current Photo" style="max-width: 120px; border: 1px solid #ddd; border-radius: 50%;">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle me-1"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // NIK validation - only numbers
        const nikInput = document.querySelector('#form-edit-user input[name="nik"]');
        if (nikInput) {
            nikInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Phone validation - only numbers
        const phoneInput = document.querySelector('#form-edit-user input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
