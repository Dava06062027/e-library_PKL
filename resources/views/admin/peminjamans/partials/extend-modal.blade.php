<div class="modal fade" id="modalExtendPeminjaman" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-extend-peminjaman" class="modal-content">
            @csrf
            <input type="hidden" name="id_peminjaman" id="extend-id-peminjaman">

            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-arrow-clockwise me-2"></i>Perpanjangan Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Info Peminjaman -->
                <div class="alert alert-info" id="extend-info-box">
                    <strong>Info Peminjaman:</strong>
                    <div class="mt-2">
                        <small>Transaksi: <strong id="extend-transaction-number">-</strong></small><br>
                        <small>Due Date Lama: <strong id="extend-due-date-lama">-</strong></small><br>
                        <small>Hari Telat: <strong id="extend-hari-telat" class="text-danger">-</strong></small><br>
                        <small>Biaya Keterlambatan: <strong id="extend-denda-telat" class="text-danger">Rp 0</strong></small>
                    </div>
                </div>

                <!-- Tanggal Kembali Rencana Baru -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Rencana Baru</label>
                    <input type="date" name="tanggal_kembali_rencana_baru" id="extend-tanggal-baru" class="form-control" required>
                    <small class="text-muted">Maksimal 5 hari dari due date lama</small>
                </div>

                <!-- Biaya Perpanjangan (Auto-calculated) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Biaya Perpanjangan</label>
                    <input type="text" id="extend-biaya-display" class="form-control" readonly>
                    <small class="text-muted">Biaya = Denda keterlambatan (jika ada)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>

                <!-- Warning Alert -->
                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Perhatian:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Perpanjangan hanya bisa <strong>maksimal 5 hari</strong> dari due date lama</li>
                        <li>Jika perpanjang setelah melewati due date, tetap kena denda sesuai hari telat</li>
                        <li>Setelah perpanjang, perhitungan denda dimulai dari due date baru</li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-lg me-1"></i>Submit Perpanjangan
                </button>
            </div>
        </form>
    </div>
</div>
