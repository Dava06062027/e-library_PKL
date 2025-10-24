<div class="modal fade" id="modalSelectMember" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Search -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="position-relative" style="width: 400px;">
                        <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-2 text-secondary"></i>
                        <input id="search-member-modal" type="text" class="form-control search-input" placeholder="Search by name or email">
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Catatan:</strong> Setiap member hanya bisa meminjam maksimal <strong>2 eksemplar</strong> secara bersamaan.
                </div>

                <!-- Table Member -->
                <div class="table-responsive bg-m365-gray border-m365 rounded">
                    <table id="member-table" class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Pinjaman Aktif</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .search-input {
        border: none;
        border-bottom: 2px solid #d1d1d1;
        border-radius: 0;
        padding-left: 32px;
        background-color: #ffffff;
    }
    .search-input:focus {
        border-bottom-color: #0078d4;
        box-shadow: none;
        background-color: #ffffff;
    }
    .bg-m365-gray { background-color: #f5f5f5 !important; }
    .border-m365 { border-color: #d1d1d1 !important; }
</style>
