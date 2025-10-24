<div class="modal fade" id="modalNewPeminjaman" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="form-new-peminjaman" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Peminjaman Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Pilih Member Section -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Member</label>
                    <input type="hidden" name="id_member" id="selected-member-id" required>

                    <div class="d-flex gap-2 align-items-center">
                        <button id="btn-select-member" type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSelectMember">
                            <i class="bi bi-person-plus"></i>
                            <span>Pilih Member</span>
                        </button>

                        <div id="selected-member-display" class="flex-grow-1" style="display:none;">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <strong id="selected-member-name"></strong>
                                                <br>
                                                <small class="text-muted" id="selected-member-email"></small>
                                            </div>
                                            <span class="badge bg-info" id="selected-member-pinjaman"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" id="btn-remove-member">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Buku & Eksemplar</label>
                    <button id="btn-add-buku" type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSelectBuku">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Buku</span>
                    </button>
                </div>

                <div id="selected-books-container" class="mb-4" style="display:none;">
                    <h6 class="fw-bold mb-3">Buku & Eksemplar Terpilih:</h6>
                    <div id="selected-books-list" class="border rounded p-3 bg-light"></div>
                    <div class="mt-2 text-muted">
                        <small>Total Eksemplar: <span id="total-eksemplar-count">0</span></small>
                    </div>
                </div>

                <!-- Tanggal Pinjam (Auto-set today, readonly) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" id="tanggal-pinjam" class="form-control" value="{{ date('Y-m-d') }}" readonly required>
                    <small class="text-muted">Otomatis diset untuk hari ini</small>
                </div>

                <!-- Tanggal Kembali Rencana (Max 7 days) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Rencana (Max 7 Hari)</label>
                    <input type="date" name="tanggal_kembali_rencana" id="tanggal-kembali-rencana" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                    <small class="text-muted">Maksimal 7 hari dari tanggal pinjam. Dapat diperpanjang max 5 hari.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-warning">
                    <strong><i class="bi bi-info-circle me-2"></i>Peraturan Peminjaman:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Periode peminjaman: <strong>Maksimal 7 hari</strong></li>
                        <li>Perpanjangan: <strong>Maksimal 5 hari</strong> (hanya bisa di hari ke 1-7)</li>
                        <li>Denda keterlambatan: <strong>Rp 1.000/hari</strong></li>
                        <li>Perpanjangan setelah hari ke-7 tetap kena denda sesuai hari telat</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Date validation for new peminjaman
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalPinjam = document.getElementById('tanggal-pinjam');
        const tanggalKembali = document.getElementById('tanggal-kembali-rencana');

        if (tanggalPinjam && tanggalKembali) {
            // Set min date for tanggal kembali = today
            const today = new Date().toISOString().split('T')[0];
            tanggalKembali.setAttribute('min', today);

            // Set max date for tanggal kembali = today + 7 days
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 7);
            const maxDateStr = maxDate.toISOString().split('T')[0];
            tanggalKembali.setAttribute('max', maxDateStr);

            // Validate on change
            tanggalKembali.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const todayDate = new Date(today);
                const maxDateCheck = new Date(maxDateStr);

                if (selectedDate < todayDate) {
                    alert('Tanggal kembali tidak boleh kurang dari hari ini!');
                    this.value = today;
                } else if (selectedDate > maxDateCheck) {
                    alert('Tanggal kembali maksimal 7 hari dari hari ini!');
                    this.value = maxDateStr;
                }
            });
        }
    });
</script>
