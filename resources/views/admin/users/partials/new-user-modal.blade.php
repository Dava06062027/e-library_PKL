<div class="modal fade" id="modalNewUser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-new-user" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Create New Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Masukkan data member sesuai dengan KTP/Kartu Pelajar yang dibawa</small>
                </div>

                <h6 class="mb-3"><i class="bi bi-person-circle me-2"></i>Data Pribadi</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" placeholder="Sesuai KTP" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input name="email" type="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input name="phone" type="text" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input name="birth_date" type="date" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Sesuai KTP/Kartu Pelajar" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input name="password" type="password" class="form-control" placeholder="Min. 8 karakter" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input name="password_confirmation" type="password" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3"><i class="bi bi-card-text me-2"></i>Data Kependudukan</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIK (16 Digit) <span class="text-danger">*</span></label>
                        <input name="nik" type="text" class="form-control" placeholder="3374012345678901" maxlength="16" required>
                        <small class="text-muted">Sesuai KTP</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="Member" selected>Member</option>
                            <option value="Officer">Officer</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Foto KTP <span class="text-danger">*</span></label>
                    <input name="ktp_photo" type="file" class="form-control" accept="image/*" required>
                    <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Foto Profil (Optional)</label>
                    <input name="photo" type="file" class="form-control" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Create Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // NIK validation - only numbers
        const nikInput = document.querySelector('#form-new-user input[name="nik"]');
        if (nikInput) {
            nikInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Phone validation - only numbers
        const phoneInput = document.querySelector('#form-new-user input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
