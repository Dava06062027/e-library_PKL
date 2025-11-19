<div class="modal fade" id="modalDetailRegistration" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2"></i>Detail Pendaftaran Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="detail-id">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="bi bi-person-circle me-2"></i>Data Pribadi</h6>

                        <div class="mb-2">
                            <strong>Nama Lengkap:</strong>
                            <p class="mb-0" id="detail-name">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Email:</strong>
                            <p class="mb-0" id="detail-email">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>No. Telepon:</strong>
                            <p class="mb-0" id="detail-phone">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Tanggal Lahir:</strong>
                            <p class="mb-0" id="detail-birth-date">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Alamat:</strong>
                            <p class="mb-0" id="detail-address">-</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-2"></i>Status & Informasi</h6>

                        <div class="mb-2">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                <span class="badge bg-info" id="detail-status">-</span>
                            </p>
                        </div>

                        <div class="mb-2">
                            <strong>No. Kartu Temporary:</strong>
                            <p class="mb-0" id="detail-temp-card">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Tanggal Daftar:</strong>
                            <p class="mb-0" id="detail-created-at">-</p>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted mb-3"><i class="bi bi-gear me-2"></i>Verifikasi Akun</h6>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Instruksi:</strong> Periksa KTP/Kartu Pelajar member. Jika data sesuai, setujui dan masukkan NIK serta foto KTP. Jika tidak sesuai, tolak pendaftaran.
                </div>

                <form id="form-approve-action" style="display: none;">
                    <input type="hidden" name="registration_id" id="approve-registration-id">

                    <div class="mb-3">
                        <label class="form-label">NIK (16 Digit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nik" id="input-nik" maxlength="16" required
                               placeholder="Contoh: 3374012345678901">
                        <small class="text-muted">Sesuai KTP/Kartu Pelajar member</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Foto KTP/Kartu Pelajar <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="ktp_photo" id="input-ktp-photo" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Setujui & Buat Akun Member
                        </button>
                        <button type="button" class="btn btn-secondary" id="btn-cancel-approve">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                    </div>
                </form>

                <form id="form-reject-action" style="display: none;">
                    <input type="hidden" name="registration_id" id="reject-registration-id">

                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan: <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" id="rejection-reason" rows="4" required
                                  placeholder="Jelaskan alasan penolakan (misal: data tidak sesuai KTP, dokumen tidak valid, dll.)"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i>Tolak Pendaftaran
                        </button>
                        <button type="button" class="btn btn-secondary" id="btn-cancel-reject">
                            <i class="bi bi-arrow-left me-2"></i>Batal
                        </button>
                    </div>
                </form>

                <div id="action-buttons">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" id="btn-show-approve">
                            <i class="bi bi-check-circle me-2"></i>Setujui Pendaftaran
                        </button>
                        <button type="button" class="btn btn-danger" id="btn-show-reject">
                            <i class="bi bi-x-circle me-2"></i>Tolak Pendaftaran
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnShowApprove = document.getElementById('btn-show-approve');
        const btnShowReject = document.getElementById('btn-show-reject');
        const btnCancelApprove = document.getElementById('btn-cancel-approve');
        const btnCancelReject = document.getElementById('btn-cancel-reject');
        const formApprove = document.getElementById('form-approve-action');
        const formReject = document.getElementById('form-reject-action');
        const actionButtons = document.getElementById('action-buttons');

        // Show approve form
        btnShowApprove.addEventListener('click', function() {
            actionButtons.style.display = 'none';
            formApprove.style.display = 'block';
            formReject.style.display = 'none';
        });

        // Show reject form
        btnShowReject.addEventListener('click', function() {
            actionButtons.style.display = 'none';
            formApprove.style.display = 'none';
            formReject.style.display = 'block';
        });

        // Cancel approve
        btnCancelApprove.addEventListener('click', function() {
            formApprove.style.display = 'none';
            actionButtons.style.display = 'block';
            formApprove.reset();
        });

        // Cancel reject
        btnCancelReject.addEventListener('click', function() {
            formReject.style.display = 'none';
            actionButtons.style.display = 'block';
            formReject.reset();
        });

        // NIK validation
        document.getElementById('input-nik').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Submit approve
        formApprove.addEventListener('submit', async function(e) {
            e.preventDefault();

            const registrationId = document.getElementById('detail-id').value;
            const formData = new FormData(this);

            try {
                const res = await fetch(`/admin/registrations/${registrationId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await res.json();

                if (!res.ok) {
                    throw new Error(result.error || result.message || 'Approval gagal');
                }

                bootstrap.Modal.getInstance(document.getElementById('modalDetailRegistration')).hide();
                alert(result.message || 'Member berhasil disetujui!');

                // Refresh list
                if (typeof fetchRegistrations === 'function') {
                    fetchRegistrations();
                } else {
                    location.reload();
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });

        // Submit reject
        formReject.addEventListener('submit', async function(e) {
            e.preventDefault();

            const registrationId = document.getElementById('detail-id').value;
            const formData = new FormData(this);

            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            if (!confirm('Yakin ingin menolak pendaftaran ini?')) return;

            try {
                const res = await fetch(`/admin/registrations/${registrationId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (!res.ok) {
                    throw new Error(result.error || result.message || 'Penolakan gagal');
                }

                bootstrap.Modal.getInstance(document.getElementById('modalDetailRegistration')).hide();
                alert(result.message || 'Pendaftaran ditolak');

                // Refresh list
                if (typeof fetchRegistrations === 'function') {
                    fetchRegistrations();
                } else {
                    location.reload();
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });
    });
</script>
