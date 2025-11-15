<div class="modal fade" id="modalReturnPeminjaman" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-return-peminjaman" class="modal-content">
            @csrf
            <input type="hidden" name="id_peminjaman" id="return-id-peminjaman">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Pengembalian Peminjaman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Transaction Info -->
                <div class="alert alert-info mb-3">
                    <strong>Transaksi:</strong> <span id="return-transaction-number">-</span><br>
                    <strong>Member:</strong> <span id="return-member-name">-</span><br>
                    <strong>Due Date:</strong> <span id="return-due-date">-</span><br>
                    <strong>Hari Telat:</strong> <span id="return-days-late" class="text-danger">-</span>
                </div>

                <!-- Items to Return -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Item yang Dikembalikan:</label>
                    <div id="return-items-container" class="border rounded p-3 bg-light">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- Return Date -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                    <input type="date" name="tanggal_kembali_aktual" id="return-date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>

                <!-- Fine Preview -->
                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Preview Denda:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Denda Keterlambatan: <strong id="return-fine-late">Rp 0</strong> (<span id="return-fine-days">0 hari</span>)</li>
                        <li>Denda Kerusakan: <strong id="return-fine-damage">Rp 0</strong></li>
                        <li class="fw-bold text-danger">Total Denda: <span id="return-fine-total">Rp 0</span></li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>Proses Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>
