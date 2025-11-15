@extends('layouts.app')

@section('content')
    <style>
        .bg-m365-gray { background-color: #f5f5f5 !important; }
        .bg-m365-white { background-color: #ffffff !important; }
        .border-m365 { border-color: #d1d1d1 !important; }
        .text-m365-blue { color: #0078d4 !important; }
        .bg-m365-blue { background-color: #0078d4 !important; }
        .bg-m365-selected { background-color: #deecf9 !important; }
        .table-hover tbody tr:hover { background-color: #e8e8e8 !important; }

        .btn-m365 {
            border: none;
            background: transparent;
            color: #323130;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn-m365:hover:not(:disabled) {
            background-color: #e8e8e8 !important;
            color: #323130;
        }
        .btn-m365:disabled {
            color: #a19f9d;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .form-check-input {
            border: 2px solid #605e5c !important;
            border-radius: 2px !important;
            width: 18px !important;
            height: 18px !important;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #0078d4 !important;
            border-color: #0078d4 !important;
        }

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

        .filter-dropdown {
            position: absolute;
            background: white;
            border: 2px solid #d1d1d1;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 15px;
            min-width: 280px;
            z-index: 1000;
            display: none;
            top: 100%;
            margin-top: 5px;
        }
        .filter-dropdown.show { display: block; }
    </style>

    <div class="bg-m365-white min-vh-100 p-4">
        <!-- Toolbar -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <button id="btn-new-peminjaman" class="btn btn-m365 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNewPeminjaman">
                <i class="bi bi-plus-lg"></i>
                <span>Peminjaman Baru</span>
            </button>
            <div class="vr"></div>
            <button id="btn-return-peminjaman" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-arrow-return-left"></i>
                <span>Pengembalian</span>
            </button>
            <button id="btn-extend-peminjaman" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-arrow-clockwise"></i>
                <span>Perpanjang</span>
            </button>
            <div class="vr"></div>
            <button id="btn-delete-peminjaman" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-trash"></i>
                <span>Delete</span>
            </button>
            <div class="vr"></div>
            <button id="btn-refresh" class="btn btn-m365 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="position-relative" style="width: 400px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-2 text-secondary"></i>
                <input id="search-peminjaman" type="text" class="form-control search-input" placeholder="Search by member, transaction #, book, barcode...">
            </div>
            <div class="position-relative">
                <button id="btn-filter" type="button" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Transaksi</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Diperpanjang">Diperpanjang</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" id="filter-tanggal" class="form-control">
                        <small class="text-muted">Filter by tanggal pinjam atau due date</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Keterlambatan</label>
                        <select id="filter-keterlambatan" class="form-select">
                            <option value="">All</option>
                            <option value="tepat_waktu">Tepat Waktu</option>
                            <option value="telat">Telat (Melewati Due Date)</option>
                        </select>
                        <small class="text-muted">Hanya untuk status "Dipinjam/Diperpanjang"</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btn-apply-filter" type="button" class="btn btn-primary flex-grow-1">Apply</button>
                        <button id="btn-clear-filter" type="button" class="btn btn-secondary">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive bg-m365-gray border-m365 rounded">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th style="width: 50px;" class="py-3">
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    <th class="py-3 fw-semibold">Nomor Transaksi</th>
                    <th class="py-3 fw-semibold">Member</th>
                    <th class="py-3 fw-semibold">Tanggal Pinjam</th>
                    <th class="py-3 fw-semibold">Due Date</th>
                    <th class="py-3 fw-semibold">Items</th>
                    <th class="py-3 fw-semibold">Status</th>
                    <th class="py-3 fw-semibold text-center">Aksi</th>
                </tr>
                </thead>
                <tbody id="peminjamans-rows">
                @include('admin.peminjamans.partials.rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-3">
            @include('admin.peminjamans.partials.pagination')
        </div>
    </div>

    <!-- Modals -->
    @include('admin.peminjamans.partials.new-modal')
    @include('admin.peminjamans.partials.return-modal')
    @include('admin.peminjamans.partials.extend-modal')
    @include('admin.peminjamans.partials.select-buku-modal')
    @include('admin.peminjamans.partials.select-member-modal')
    @include('admin.peminjamans.partials.detail-modal')

@endsection

@push('scripts')
    <script>
        // ✅ DEFINISIKAN FUNCTION DI GLOBAL SCOPE DULU
        window.fetchPeminjamans = null; // Deklarasi early

        (function(){
            // DOM Elements
            const searchInput = document.getElementById('search-peminjaman');
            const selectAll = document.getElementById('select-all');
            const btnReturn = document.getElementById('btn-return-peminjaman');
            const btnExtend = document.getElementById('btn-extend-peminjaman');
            const btnDelete = document.getElementById('btn-delete-peminjaman');
            const btnRefresh = document.getElementById('btn-refresh');
            const btnFilter = document.getElementById('btn-filter');
            const filterDropdown = document.getElementById('filter-dropdown');

            // Filter state
            let currentFilters = {
                search: '',
                status: '',
                tanggal: '',
                keterlambatan: ''
            };

            // Debounce utility
            function debounce(fn, delay = 300) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn(...args), delay);
                };
            }

            // ✅ DEFINISIKAN FUNCTION DI DALAM IIFE
            async function fetchPeminjamans(url = "{{ route('admin.peminjamans.index') }}") {
                try {
                    const params = new URLSearchParams();

                    if (currentFilters.search) params.append('search', currentFilters.search);
                    if (currentFilters.status) params.append('status', currentFilters.status);
                    if (currentFilters.tanggal) params.append('tanggal', currentFilters.tanggal);
                    if (currentFilters.keterlambatan) params.append('keterlambatan', currentFilters.keterlambatan);

                    const fullUrl = url.includes('?') ? `${url}&${params}` : `${url}?${params}`;

                    const res = await fetch(fullUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Network response was not ok');

                    const html = await res.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newRows = doc.querySelector('#peminjamans-rows');
                    const newPagination = doc.querySelector('#pagination');

                    if (newRows) {
                        document.getElementById('peminjamans-rows').innerHTML = newRows.innerHTML;
                    }

                    if (newPagination) {
                        document.getElementById('pagination').innerHTML = newPagination.innerHTML;
                    }

                    attachRowHandlers();
                    updateButtonStates();

                } catch (err) {
                    console.error('Fetch error:', err);
                    document.getElementById('peminjamans-rows').innerHTML =
                        '<tr><td colspan="8" class="text-center text-danger py-4">Error loading data. Please refresh.</td></tr>';
                }
            }

            // ✅ ASSIGN KE WINDOW OBJECT DI DALAM IIFE
            window.fetchPeminjamans = fetchPeminjamans;

            // Search handler
            searchInput.addEventListener('input', debounce(function() {
                currentFilters.search = this.value.trim();
                fetchPeminjamans();
            }, 300));

            // Filter dropdown toggle
            btnFilter.addEventListener('click', function(e) {
                e.stopPropagation();
                filterDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!filterDropdown.contains(e.target) && e.target !== btnFilter) {
                    filterDropdown.classList.remove('show');
                }
            });

            // Apply filters
            document.getElementById('btn-apply-filter').addEventListener('click', function() {
                currentFilters.status = document.getElementById('filter-status').value;
                currentFilters.tanggal = document.getElementById('filter-tanggal').value;
                currentFilters.keterlambatan = document.getElementById('filter-keterlambatan').value;

                fetchPeminjamans();
                filterDropdown.classList.remove('show');
            });

            // Clear filters
            document.getElementById('btn-clear-filter').addEventListener('click', function() {
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-tanggal').value = '';
                document.getElementById('filter-keterlambatan').value = '';

                currentFilters.status = '';
                currentFilters.tanggal = '';
                currentFilters.keterlambatan = '';

                fetchPeminjamans();
                filterDropdown.classList.remove('show');
            });

            // Pagination click handler
            document.addEventListener('click', function(e) {
                const paginationLink = e.target.closest('#pagination a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    fetchPeminjamans(paginationLink.href);
                }

                // Detail button handler
                const btnDetail = e.target.closest('.btn-view-detail');
                if (btnDetail) {
                    const id = btnDetail.getAttribute('data-id');
                    showDetailModal(id);
                }
            });

            // Refresh button
            btnRefresh.addEventListener('click', function() {
                fetchPeminjamans();
            });

            // Select all checkbox
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.select-peminjaman').forEach(checkbox => {
                    checkbox.checked = this.checked;
                    checkbox.closest('tr').classList.toggle('bg-m365-selected', this.checked);
                });
                updateButtonStates();
            });

            // Attach row handlers
            function attachRowHandlers() {
                document.querySelectorAll('.select-peminjaman').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        this.closest('tr').classList.toggle('bg-m365-selected', this.checked);

                        if (!this.checked) {
                            selectAll.checked = false;
                        }

                        updateButtonStates();
                    });
                });
            }

            // Update button states
            function updateButtonStates() {
                const selected = document.querySelectorAll('.select-peminjaman:checked');
                const count = selected.length;

                if (count === 0) {
                    btnReturn.disabled = true;
                    btnExtend.disabled = true;
                    btnDelete.disabled = true;
                    return;
                }

                const statuses = Array.from(selected).map(cb =>
                    cb.closest('tr').getAttribute('data-status')
                );

                const allActive = statuses.every(s => s === 'Dipinjam' || s === 'Diperpanjang');
                btnReturn.disabled = !allActive;

                btnExtend.disabled = !(count === 1 && (statuses[0] === 'Dipinjam' || statuses[0] === 'Diperpanjang'));

                btnDelete.disabled = false;
            }

            // Detail modal
            async function showDetailModal(id) {
                try {
                    const res = await fetch(`{{ url('admin/peminjamans') }}/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to fetch detail');

                    const data = await res.json();

                    document.getElementById('detail-transaction-number').textContent = data.transaction_number;
                    document.getElementById('detail-member-name').textContent = data.member_name;
                    document.getElementById('detail-member-email').textContent = data.member_email;
                    document.getElementById('detail-officer-name').textContent = data.officer_name;
                    document.getElementById('detail-tanggal-pinjam').textContent = data.tanggal_pinjam;
                    document.getElementById('detail-due-date').textContent = data.tanggal_kembali_rencana;
                    document.getElementById('detail-status').textContent = data.status_transaksi;
                    document.getElementById('detail-total-items').textContent = `${data.items_dikembalikan}/${data.total_items}`;
                    document.getElementById('detail-total-denda').textContent = `Rp ${data.total_denda}`;
                    document.getElementById('detail-catatan').textContent = data.catatan;

                    if (data.days_late > 0) {
                        document.getElementById('detail-days-late').textContent = `${data.days_late} hari`;
                        document.getElementById('detail-days-late').classList.remove('d-none');
                    } else {
                        document.getElementById('detail-days-late').classList.add('d-none');
                    }

                    const itemsList = document.getElementById('detail-items-list');
                    itemsList.innerHTML = data.items.map(item => `
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${item.buku_judul}</strong>
                            <br><small class="text-muted">Barcode: ${item.barcode}</small>
                            ${item.tanggal_kembali_aktual ? `<br><small>Dikembalikan: ${item.tanggal_kembali_aktual}</small>` : ''}
                        </div>
                        <div class="text-end">
                            <span class="badge ${item.status_item === 'Dipinjam' ? 'bg-warning text-dark' : 'bg-success'}">${item.status_item}</span>
                            ${item.total_denda_item && parseFloat(item.total_denda_item.replace(/\./g, '')) > 0 ? `<br><small class="text-danger">Denda: Rp ${item.total_denda_item}</small>` : ''}
                        </div>
                    </div>
                </li>
            `).join('');

                    if (data.perpanjangans && data.perpanjangans.length > 0) {
                        document.getElementById('detail-perpanjangan-section').style.display = 'block';
                        const perpanjanganList = document.getElementById('detail-perpanjangan-list');
                        perpanjanganList.innerHTML = data.perpanjangans.map(ext => `
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <small>
                                <strong>Tanggal:</strong> ${ext.tanggal_perpanjangan}<br>
                                <strong>Due Date:</strong> ${ext.due_date_lama} → ${ext.due_date_baru} (+${ext.hari_perpanjangan} hari)<br>
                                <strong>Biaya:</strong> Rp ${ext.biaya}<br>
                                <strong>Petugas:</strong> ${ext.officer_name}
                            </small>
                        </div>
                    </div>
                `).join('');
                    } else {
                        document.getElementById('detail-perpanjangan-section').style.display = 'none';
                    }

                    new bootstrap.Modal(document.getElementById('modalDetailPeminjaman')).show();

                } catch (err) {
                    console.error('Detail modal error:', err);
                    alert('Failed to load details: ' + err.message);
                }
            }

            // Delete handler
            btnDelete.addEventListener('click', async function() {
                const selected = Array.from(document.querySelectorAll('.select-peminjaman:checked')).map(cb => cb.value);

                if (selected.length === 0) {
                    alert('Pilih setidaknya satu transaksi!');
                    return;
                }

                if (!confirm(`Yakin hapus ${selected.length} transaksi?`)) {
                    return;
                }

                try {
                    const res = await fetch('{{ route("admin.peminjamans.destroySelected") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: selected })
                    });

                    const data = await res.json();

                    if (!res.ok) throw new Error(data.error || 'Failed to delete');

                    alert(data.message);
                    selectAll.checked = false;
                    await fetchPeminjamans();

                } catch (err) {
                    console.error('Delete error:', err);
                    alert('Error: ' + err.message);
                }
            });

            // Initialize
            attachRowHandlers();
            updateButtonStates();

        })();

    </script>

    @include('admin.peminjamans.partials.modal-scripts')
@endpush
