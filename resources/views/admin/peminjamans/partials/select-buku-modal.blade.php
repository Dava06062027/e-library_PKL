<div class="modal fade" id="modalSelectBuku" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Buku & Eksemplar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Section 1: Pilih Judul Buku -->
                <div id="section-pilih-judul">
                    <h6 class="fw-bold mb-3">1. Pilih Judul Buku</h6>

                    <!-- Search & Filter -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="position-relative" style="width: 300px;">
                            <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-2 text-secondary"></i>
                            <input id="search-buku-modal" type="text" class="form-control search-input" placeholder="Search by judul or pengarang">
                        </div>
                        <div class="position-relative">
                            <button id="btn-filter-buku" type="button" class="btn btn-m365 d-flex align-items-center gap-2">
                                <i class="bi bi-funnel"></i>
                                <span>Add filter</span>
                            </button>
                            <div id="filter-dropdown-buku" class="filter-dropdown">
                                <div class="mb-2">
                                    <label>Tahun Terbit</label>
                                    <input type="number" id="filter-tahun" class="form-control" placeholder="YYYY">
                                </div>
                                <button id="btn-apply-filter-buku" type="button" class="btn btn-primary w-100">Apply</button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Buku -->
                    <div class="table-responsive bg-m365-gray border-m365 rounded">
                        <table id="buku-table" class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Tahun Terbit</th>
                                <th>Eksemplar Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 2: Pilih Eksemplar (Hidden by default) -->
                <div id="section-pilih-eksemplar" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">2. Pilih Eksemplar dari: <span id="selected-judul-text"></span></h6>
                            <small class="text-muted">Pilih eksemplar yang akan dipinjam (hanya Tersedia)</small>
                        </div>
                        <button type="button" id="btn-back-to-judul" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Buku
                        </button>
                    </div>

                    <!-- Range Barcode Input -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <label class="form-label">Quick Select by Range Barcode</label>
                            <div class="input-group">
                                <input id="range-barcode" class="form-control" placeholder="Contoh: 202510220301001-202510220301005">
                                <button type="button" id="btn-apply-range" class="btn btn-secondary">Apply Range</button>
                            </div>
                            <small class="text-muted">Format: BARCODE_AWAL-BARCODE_AKHIR</small>
                        </div>
                    </div>

                    <!-- Table Eksemplar -->
                    <div class="table-responsive bg-m365-gray border-m365 rounded">
                        <table id="eksemplar-table" class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="select-all-eksemplar" class="form-check-input">
                                </th>
                                <th>Barcode</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th>Sumber</th>
                            </tr>
                            </thead>
                            <tbody id="eksemplar-table-body">
                            <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="btn-confirm-eksemplar" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Konfirmasi Pilihan (<span id="selected-eksemplar-count">0</span> Eksemplar)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
