// Directory: resources/views/admin/peminjamans/index.blade.php
@extends('layouts.app')

@section('content')
    <style>
        /* Minimal custom styles untuk warna yang spesifik Microsoft 365 */
        .bg-m365-gray { background-color: #f5f5f5 !important; } /* Lebih grey untuk table */
        .bg-m365-white { background-color: #ffffff !important; } /* Putih bersih untuk background */
        .border-m365 { border-color: #d1d1d1 !important; } /* Border lebih kontras */
        .text-m365-blue { color: #0078d4 !important; }
        .bg-m365-blue { background-color: #0078d4 !important; }
        .bg-m365-selected { background-color: #deecf9 !important; }
        .table-hover tbody tr:hover { background-color: #e8e8e8 !important; }
        .btn-m365 {
            border: none;
            background: transparent;
            color: #323130;
        }
        .btn-m365:hover:not(:disabled) {
            background-color: #e8e8e8 !important;
            color: #323130;
        }
        .btn-m365:disabled { color: #a19f9d; }

        /* Checkbox styling - more bold and contrast */
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
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 120, 212, 0.25) !important;
        }
        .form-check-input:hover {
            border-color: #323130 !important;
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
        .status-online {
            width: 10px;
            height: 10px;
            background-color: #92c353;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-offline {
            width: 10px;
            height: 10px;
            background-color: #d1d1d1;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .copy-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #107c10;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            display: none;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .copy-notification.show {
            display: flex;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        .filter-dropdown {
            position: absolute;
            background: white;
            border: 2px solid #d1d1d1;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 15px;
            min-width: 250px;
            z-index: 1000;
            display: none;
        }
        .filter-dropdown.show { display: block; }
    </style>

    <div class="bg-m365-white min-vh-100 p-4">
        <!-- Copy Notification -->
        <div id="copy-notification" class="copy-notification">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M13.5 4.5L6 12L2.5 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Copied!</span>
        </div>

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
            <div class="position-relative" style="width: 300px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-2 text-secondary"></i>
                <input id="search-peminjaman" type="text" class="form-control search-input" placeholder="Search by member, barcode or status">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-2">
                        <label>Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                            <option value="Telat">Telat</option>
                            <option value="Hilang">Hilang</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Role User</label>
                        <select id="filter-role" class="form-select">
                            <option value="">All</option>
                            <option value="Admin">Admin</option>
                            <option value="Officer">Officer</option>
                            <option value="Member">Member</option>
                        </select>
                    </div>
                    <button id="btn-apply-filter" class="btn btn-primary w-100">Apply</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive bg-m365-gray border-m365 rounded">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="select-all" class="form-check-input"></th>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Eksemplar Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Total Denda</th>
                    <th>Petugas</th>
                    <th>Modified Date</th>
                </tr>
                </thead>
                <tbody id="peminjamans-rows">
                <!-- Rows will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-3"></div>

        <!-- Include Modals -->
        @include('admin.peminjamans.partials.new-modal')
        @include('admin.peminjamans.partials.return-modal')
        @include('admin.peminjamans.partials.extend-modal')
        @include('admin.peminjamans.partials.select-buku-modal')
    </div>

    @push('scripts')
        <script>
            (() => {
                const BASE_URL = '{{ route('admin.peminjamans.index') }}';
                const $tableBody = document.getElementById('peminjamans-rows');
                const $pagination = document.getElementById('pagination');
                const $btnNew = document.getElementById('btn-new-peminjaman');
                const $btnReturn = document.getElementById('btn-return-peminjaman');
                const $btnExtend = document.getElementById('btn-extend-peminjaman');
                const $btnDelete = document.getElementById('btn-delete-peminjaman');
                const $btnRefresh = document.getElementById('btn-refresh');
                const $searchInput = document.getElementById('search-peminjaman');
                const $btnFilter = document.getElementById('btn-filter');
                const $filterDropdown = document.getElementById('filter-dropdown');
                const $btnApplyFilter = document.getElementById('btn-apply-filter');
                const $selectAll = document.getElementById('select-all');
                const csrf = '{{ csrf_token() }}';

                let currentPage = 1;
                let currentFilters = {};
                let selectedPeminjamans = [];

                // Fetch peminjamans
                async function fetchPeminjamans(filters = {}) {
                    try {
                        const params = new URLSearchParams({
                            page: currentPage,
                            ...filters
                        });
                        const res = await fetch(`${BASE_URL}?${params.toString()}`);
                        const html = await res.text();
                        $tableBody.innerHTML = html;

                        // Update pagination (assume backend sends pagination HTML)
                        $pagination.innerHTML = document.querySelector('#pagination').outerHTML;  // Adjust if needed

                        attachCheckboxListeners();
                        updateButtons();
                    } catch (err) {
                        console.error('Fetch error:', err);
                    }
                }

                // Initial load
                fetchPeminjamans();

                // Refresh
                $btnRefresh.addEventListener('click', () => fetchPeminjamans(currentFilters));

                // Search
                $searchInput.addEventListener('input', debounce((e) => {
                    currentFilters.search = e.target.value;
                    fetchPeminjamans(currentFilters);
                }, 300));

                // Filter dropdown
                $btnFilter.addEventListener('click', () => $filterDropdown.classList.toggle('show'));
                $btnApplyFilter.addEventListener('click', () => {
                    currentFilters.status = document.getElementById('filter-status').value;
                    currentFilters.role = document.getElementById('filter-role').value;
                    $filterDropdown.classList.remove('show');
                    fetchPeminjamans(currentFilters);
                });

                // Select all
                $selectAll.addEventListener('change', (e) => {
                    document.querySelectorAll('.select-peminjaman').forEach(cb => cb.checked = e.target.checked);
                    updateSelected();
                });

                function attachCheckboxListeners() {
                    document.querySelectorAll('.select-peminjaman').forEach(cb => {
                        cb.addEventListener('change', updateSelected);
                    });
                }

                function updateSelected() {
                    selectedPeminjamans = Array.from(document.querySelectorAll('.select-peminjaman:checked')).map(cb => cb.value);
                    updateButtons();
                }

                function updateButtons() {
                    const hasSelection = selectedPeminjamans.length > 0;
                    $btnReturn.disabled = !hasSelection;
                    $btnExtend.disabled = !hasSelection;
                    $btnDelete.disabled = !hasSelection;
                }

                // Handle return (open modal with selected ID, assume single for simplicity)
                $btnReturn.addEventListener('click', () => {
                    if (selectedPeminjamans.length !== 1) return alert('Pilih 1 peminjaman!');
                    document.getElementById('return-id-peminjaman').value = selectedPeminjamans[0];
                    new bootstrap.Modal(document.getElementById('modalReturnPeminjaman')).show();
                });

                // Handle extend
                $btnExtend.addEventListener('click', () => {
                    if (selectedPeminjamans.length !== 1) return alert('Pilih 1 peminjaman!');
                    document.getElementById('extend-id-peminjaman').value = selectedPeminjamans[0];
                    new bootstrap.Modal(document.getElementById('modalExtendPeminjaman')).show();
                });

                // Handle delete (bulk)
                $btnDelete.addEventListener('click', async () => {
                    if (!confirm('Yakin delete?')) return;
                    try {
                        const res = await fetch('{{ route('admin.peminjamans.destroySelected') }}', {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ids: selectedPeminjamans })
                        });
                        if (res.ok) fetchPeminjamans(currentFilters);
                    } catch (err) {
                        console.error(err);
                    }
                });

                // Submit new peminjaman (adaptasi dari bulk tataraks)
                $('#form-new-peminjaman').on('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const allEksemplarIds = [];  // Kumpul dari selectedBooksData seperti di tataraks
                    // Assume selectedBooksData from select modal
                    Object.values(selectedBooksData).forEach(data => allEksemplarIds.push(...data.eksemplar));
                    formData.append('id_buku_items', allEksemplarIds);

                    try {
                        const res = await fetch('{{ route('admin.peminjamans.store') }}', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();
                        if (res.ok) {
                            $('#modalNewPeminjaman').modal('hide');
                            fetchPeminjamans(currentFilters);
                        } else {
                            alert(data.message || 'Error');
                        }
                    } catch (err) {
                        alert('Error');
                    }
                });

                // Submit return
                $('#form-return-peminjaman').on('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const res = await fetch('{{ route('admin.peminjamans.return') }}', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();
                        if (res.ok) {
                            $('#modalReturnPeminjaman').modal('hide');
                            fetchPeminjamans(currentFilters);
                        } else {
                            alert(data.message || 'Error');
                        }
                    } catch (err) {
                        alert('Error');
                    }
                });

                // Submit extend
                $('#form-extend-peminjaman').on('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const res = await fetch('{{ route('admin.peminjamans.extend') }}', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();
                        if (res.ok) {
                            $('#modalExtendPeminjaman').modal('hide');
                            fetchPeminjamans(currentFilters);
                        } else {
                            alert(data.message || 'Error');
                        }
                    } catch (err) {
                        alert('Error');
                    }
                });

                // Select buku modal logic (copy from tataraks, adjust URLs to peminjamans.bukus and peminjamans.eksemplars)
                // ... (paste and adapt the script from tataraks for select buku and eksemplar)

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }
            })();
        </script>
    @endpush
@endsection
