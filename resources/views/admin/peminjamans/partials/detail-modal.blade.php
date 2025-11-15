<div class="modal fade" id="modalDetailPeminjaman" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detail Transaksi Peminjaman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Transaction Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="bi bi-receipt me-2"></i>Informasi Transaksi</h6>

                        <div class="mb-2">
                            <strong>Nomor Transaksi:</strong>
                            <p class="mb-0">
                                <span class="badge bg-primary" id="detail-transaction-number">-</span>
                            </p>
                        </div>

                        <div class="mb-2">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                <span class="badge bg-primary" id="detail-status">-</span>
                                <span class="badge bg-danger ms-2 d-none" id="detail-days-late">0 hari</span>
                            </p>
                        </div>

                        <div class="mb-2">
                            <strong>Progress Pengembalian:</strong>
                            <p class="mb-0" id="detail-total-items">0/0</p>
                        </div>

                        <div class="mb-2">
                            <strong>Total Denda:</strong>
                            <p class="mb-0 text-danger fw-bold" id="detail-total-denda">Rp 0</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3"><i class="bi bi-person-circle me-2"></i>Informasi Member</h6>

                        <div class="mb-2">
                            <strong>Nama Member:</strong>
                            <p class="mb-0" id="detail-member-name">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Email:</strong>
                            <p class="mb-0 small" id="detail-member-email">-</p>
                        </div>

                        <div class="mb-2">
                            <strong>Petugas:</strong>
                            <p class="mb-0" id="detail-officer-name">-</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Date Info -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3"><i class="bi bi-calendar-event me-2"></i>Informasi Tanggal</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Tanggal Pinjam:</strong>
                                <p class="mb-0" id="detail-tanggal-pinjam">-</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Due Date:</strong>
                                <p class="mb-0" id="detail-due-date">-</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Catatan:</strong>
                                <p class="mb-0 small text-muted" id="detail-catatan">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Items List -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3"><i class="bi bi-bookshelf me-2"></i>Items Dipinjam</h6>
                        <ul id="detail-items-list" class="list-group">
                            <!-- Populated by JavaScript -->
                        </ul>
                    </div>
                </div>

                <!-- Perpanjangan History -->
                <div id="detail-perpanjangan-section" style="display: none;">
                    <hr class="my-4">
                    <h6 class="text-muted mb-3"><i class="bi bi-calendar-plus me-2"></i>Riwayat Perpanjangan</h6>
                    <div id="detail-perpanjangan-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
