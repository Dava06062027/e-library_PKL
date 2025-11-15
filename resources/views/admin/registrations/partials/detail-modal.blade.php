<div class="modal fade" id="modalDetailRegistration" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2"></i>Detail Pendaftaran</h5>
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
                    </div>
                </div>

                <hr>

                <h6 class="text-muted mb-3"><i class="bi bi-file-earmark-text me-2"></i>Dokumen</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Dokumen Identitas:</strong><br>
                        <a href="#" id="detail-id-doc" target="_blank" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                            <i class="bi bi-download me-1"></i>Lihat Dokumen
                        </a>
                    </div>

                    <div class="col-md-6 mb-3">
                        <strong>Bukti Alamat:</strong><br>
                        <a href="#" id="detail-address-doc" target="_blank" class="btn btn-sm btn-outline-primary mt-2" style="display: none;">
                            <i class="bi bi-download me-1"></i>Lihat Dokumen
                        </a>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted mb-3"><i class="bi bi-gear me-2"></i>Tindakan</h6>

                <form id="form-review-action">
                    <input type="hidden" name="registration_id" id="review-registration-id">

                    <div class="mb-3">
                        <label class="form-label">Pilih Tindakan:</label>
                        <select class="form-select" name="action" id="review-action" required>
                            <option value="">-- Pilih --</option>
                            <option value="approve_review">✓ Setujui Dokumen (Lanjut ke Approval)</option>
                            <option value="request_documents">📄 Minta Dokumen Tambahan</option>
                            <option value="reject">✗ Tolak Pendaftaran</option>
                        </select>
                    </div>

                    <div class="mb-3" id="notes-container" style="display: none;">
                        <label class="form-label">Catatan:</label>
                        <textarea class="form-control" name="notes" id="review-notes" rows="3"
                                  placeholder="Catatan untuk member (opsional)"></textarea>
                    </div>

                    <div class="mb-3" id="rejection-container" style="display: none;">
                        <label class="form-label">Alasan Penolakan: <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" id="rejection-reason" rows="3"
                                  placeholder="Jelaskan alasan penolakan kepada member"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Submit Tindakan
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewAction = document.getElementById('review-action');
        const notesContainer = document.getElementById('notes-container');
        const rejectionContainer = document.getElementById('rejection-container');

        if (reviewAction) {
            reviewAction.addEventListener('change', function() {
                const action = this.value;

                // Hide all containers first
                notesContainer.style.display = 'none';
                rejectionContainer.style.display = 'none';

                // Show relevant container
                if (action === 'request_documents') {
                    notesContainer.style.display = 'block';
                } else if (action === 'reject') {
                    rejectionContainer.style.display = 'block';
                }
            });
        }

        const formReview = document.getElementById('form-review-action');
        if (formReview) {
            formReview.addEventListener('submit', async function(e) {
                e.preventDefault();

                const registrationId = document.getElementById('detail-id').value;
                const formData = new FormData(this);

                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                try {
                    const res = await fetch(`/admin/registrations/${registrationId}/review`, {
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
                        throw new Error(result.error || result.message || 'Tindakan gagal');
                    }

                    bootstrap.Modal.getInstance(document.getElementById('modalDetailRegistration')).hide();
                    alert(result.message || 'Tindakan berhasil!');

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
        }
    });
</script>
