<!-- resources/views/raks/index.blade.php -->

@extends('layouts.app')

@section('content')
    <style>
        /* Sama seperti contoh: Minimal custom styles untuk warna M365-ish */
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
        }
        .btn-m365:hover:not(:disabled) {
            background-color: #e8e8e8 !important;
            color: #323130;
        }
        .btn-m365:disabled { color: #a19f9d; }
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
        }
        .filter-dropdown {
            position: absolute;
            background: white;
            border: 2px solid #d1d1d1;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 16px;
            min-width: 250px;
            z-index: 1000;
            display: none;
            margin-top: 4px;
        }
        .filter-dropdown.show {
            display: block;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 16px 0;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            background-color: #ffffff;
            border: 1px solid #d1d1d1;
            color: #0078d4;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .pagination .page-link:hover {
            background-color: #e8e8e8;
        }

        .pagination .active .page-link {
            background-color: #0078d4;
            color: #ffffff;
            border-color: #0078d4;
        }

        .pagination .disabled .page-link {
            color: #a19f9d;
            pointer-events: none;
        }

        /* Hide or override any weird entities/icons */
        .pagination .page-link span {
            display: inline-block;
            font-family: sans-serif; /* Force standard font to avoid icon overrides */
        }
    </style>

    <div class="bg-m365-white min-vh-100 p-4">
        <!-- Toolbar -->
        <div class="d-flex align-items-center gap-2 mb-3">
            @if(auth()->user()->role === 'Officer' || auth()->user()->role === 'Admin')
                <button id="btn-new-rak" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Rak</span>
                </button>
                <div class="vr"></div>
                <button id="btn-detail-rak" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-book"></i>
                    <span>Detail</span>
                </button>
                <button id="btn-edit-rak" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-pencil"></i>
                    <span>Edit</span>
                </button>
                <button id="btn-delete-rak" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-trash"></i>
                    <span>Delete</span>
                </button>
                <div class="vr"></div>
            @endif
            <button id="btn-refresh" class="btn btn-m365 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="position-relative" style="width: 300px;">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y ms-2 text-secondary"></i>
                <input id="search-rak" type="text" class="form-control search-input" placeholder="Search by Nama or Barcode">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Lokasi</label>
                        <select id="filter-lokasi" class="form-select form-select-sm">
                            <option value="">All Lokasi</option>
                            @foreach($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->ruang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Kategori</label>
                        <select id="filter-kategori" class="form-select form-select-sm">
                            <option value="">All Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btn-apply-filter" class="btn btn-primary btn-sm flex-grow-1">Apply</button>
                        <button id="btn-clear-filter" class="btn btn-secondary btn-sm">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Count -->
        <div class="text-secondary small mb-3">
            <span id="rak-count">{{ $raks->total() }} raks found</span>
        </div>

        <!-- Table -->
        <div class="bg-m365-gray border border-m365 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-m365-gray border-bottom border-m365">
                    <tr>
                        <th style="width: 50px;" class="py-3">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th class="py-3 fw-semibold">Nama</th>
                        <th class="py-3 fw-semibold">Barcode</th>
                        <th class="py-3 fw-semibold">Kolom</th>
                        <th class="py-3 fw-semibold">Baris</th>
                        <th class="py-3 fw-semibold">Kapasitas</th>
                        <th class="py-3 fw-semibold">Lokasi</th>
                        <th class="py-3 fw-semibold">Kategori</th>
                        <th class="py-3 fw-semibold text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="rak-table-body">
                    @include('raks.partials.rows')
                    </tbody>
                </table>
            </div>
            @include('raks.partials.pagination')
        </div>
    </div>

    @include('raks.partials.new-rak-modal')
    @include('raks.partials.edit-rak-modal')
    @include('raks.partials.rak-detail-modal')
    @include('raks.partials.items-list-modal')

    @push('scripts')
        <script>
            (function() {
                const csrf = '{{ csrf_token() }}';
                const $selectAll = document.getElementById('select-all');
                const $btnNew = document.getElementById('btn-new-rak');
                const $btnDetail = document.getElementById('btn-detail-rak');
                const $btnEdit = document.getElementById('btn-edit-rak');
                const $btnDelete = document.getElementById('btn-delete-rak');
                const $btnRefresh = document.getElementById('btn-refresh');
                const $searchInput = document.getElementById('search-rak');
                const $btnFilter = document.getElementById('btn-filter');
                const $filterDropdown = document.getElementById('filter-dropdown');
                const $filterLokasi = document.getElementById('filter-lokasi');
                const $filterKategori = document.getElementById('filter-kategori');
                const $btnApplyFilter = document.getElementById('btn-apply-filter');
                const $btnClearFilter = document.getElementById('btn-clear-filter');
                const $rakCount = document.getElementById('rak-count');
                const $tableBody = document.getElementById('rak-table-body');

                let currentPage = 1;
                let searchQuery = '';
                let filterLokasiVal = '';
                let filterKategoriVal = '';

                // Toggle buttons based on selection
                function toggleButtons() {
                    const selected = getSelectedIds().length;
                    if ($btnDetail) $btnDetail.disabled = selected !== 1;
                    if ($btnEdit) $btnEdit.disabled = selected !== 1;
                    if ($btnDelete) $btnDelete.disabled = selected === 0;
                }

                function getSelectedIds() {
                    return Array.from(document.querySelectorAll('.select-rak:checked')).map(el => el.value);
                }

                function attachRowHandlers() {
                    document.querySelectorAll('.select-rak').forEach(el => {
                        el.addEventListener('change', toggleButtons);
                    });
                    $selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.select-rak').forEach(el => el.checked = this.checked);
                        toggleButtons();
                    });
                }

                function attachDetailHandlers() {
                    document.querySelectorAll('.btn-view-detail').forEach(btn => {
                        btn.addEventListener('click', showRakDetail);
                    });
                    document.querySelectorAll('.btn-view-items').forEach(btn => {
                        btn.addEventListener('click', showItemsList);
                    });
                }

                // Fetch raks with AJAX for partial HTML
                async function fetchRaks(page = 1) {
                    try {
                        const params = new URLSearchParams({
                            page,
                            search: searchQuery,
                            lokasi: filterLokasiVal,
                            kategori: filterKategoriVal
                        });
                        const res = await fetch(`/raks?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const html = await res.text();
                        $tableBody.innerHTML = html;
                        attachRowHandlers();
                        attachDetailHandlers();
                        toggleButtons();
                        // Update count approximately
                        $rakCount.textContent = document.querySelectorAll('#rak-table-body tr:not(.no-data)').length + ' raks found';
                    } catch (err) {
                        console.error('Failed to fetch raks', err);
                        alert('Failed to fetch raks');
                    }
                }

                // Show rak detail with JSON
                async function showRakDetail(e) {
                    const id = e.target.closest('button').dataset.id;
                    try {
                        const res = await fetch(`/raks/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const rak = await res.json();
                        document.getElementById('detail-nama').textContent = rak.nama || '-';
                        document.getElementById('detail-barcode').textContent = rak.barcode || '-';
                        document.getElementById('detail-kolom').textContent = rak.kolom || '-';
                        document.getElementById('detail-baris').textContent = rak.baris || '-';
                        document.getElementById('detail-kapasitas').textContent = rak.kapasitas || '-';
                        document.getElementById('detail-lokasi').textContent = rak.lokasi?.ruang || '-';
                        document.getElementById('detail-kategori').textContent = rak.kategori?.nama || '-';
                        new bootstrap.Modal(document.getElementById('modalRakDetail')).show();
                    } catch (err) {
                        alert('Failed to fetch rak detail: ' + err.message);
                    }
                }

                async function showItemsList(e) {
                    const id = e.target.closest('button').dataset.id;
                    try {
                        const res = await fetch(`/raks/${id}/bukuitems`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const items = await res.json();
                        const $itemsBody = document.getElementById('items-table-body');
                        $itemsBody.innerHTML = '';
                        if (items.length === 0) {
                            $itemsBody.innerHTML = '<tr><td colspan="5" class="text-center">No items found.</td></tr>';
                        } else {
                            items.forEach(item => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                        <td>${item.barcode}</td>
                        <td><span class="badge bg-${item.kondisi === 'Baik' ? 'success' : item.kondisi === 'Rusak' ? 'warning' : 'danger'}">${item.kondisi}</span></td>
                        <td><span class="badge bg-${item.status === 'Tersedia' ? 'primary' : item.status === 'Dipinjam' ? 'info' : 'secondary'}">${item.status}</span></td>
                        <td>${item.sumber}</td>
                        <td>${item.buku?.judul || '-'}</td>
                    `;
                                $itemsBody.appendChild(tr);
                            });
                        }
                        new bootstrap.Modal(document.getElementById('modalItemsList')).show();
                    } catch (err) {
                        alert('Failed to fetch items: ' + err.message);
                    }
                }

                // New Rak
                if ($btnNew) {
                    $btnNew.addEventListener('click', () => {
                        new bootstrap.Modal(document.getElementById('modalNewRak')).show();
                    });
                }

                // Form submit for new with FormData and proper headers
                document.getElementById('form-new-rak').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const res = await fetch('/raks', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        if (!res.ok) {
                            const errorData = await res.json();
                            throw new Error(errorData.message || 'Failed to create');
                        }
                        const data = await res.json();
                        fetchRaks(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalNewRak')).hide();
                        alert('Created successfully');
                    } catch (err) {
                        alert('Failed to create: ' + err.message);
                    }
                });

                // Edit Rak
                if ($btnEdit) {
                    $btnEdit.addEventListener('click', async function() {
                        const id = getSelectedIds()[0];
                        try {
                            const res = await fetch(`/raks/${id}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (!res.ok) throw new Error('Failed to fetch');
                            const rak = await res.json();
                            document.getElementById('edit-rak-id').value = rak.id;
                            document.getElementById('edit-nama').value = rak.nama;
                            document.getElementById('edit-barcode').value = rak.barcode;
                            document.getElementById('edit-kolom').value = rak.kolom;
                            document.getElementById('edit-baris').value = rak.baris;
                            document.getElementById('edit-kapasitas').value = rak.kapasitas;
                            document.getElementById('edit-id-lokasi').value = rak.id_lokasi;
                            document.getElementById('edit-id-kategori').value = rak.id_kategori;
                            new bootstrap.Modal(document.getElementById('modalEditRak')).show();
                        } catch (err) {
                            alert('Failed to load edit data: ' + err.message);
                        }
                    });
                }

                // Form submit for edit with FormData and proper headers
                document.getElementById('form-edit-rak').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit-rak-id').value;
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    try {
                        const res = await fetch(`/raks/${id}`, {
                            method: 'POST', // Since Laravel uses POST for PUT with _method
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        if (!res.ok) {
                            const errorData = await res.json();
                            throw new Error(errorData.message || 'Failed to update');
                        }
                        const data = await res.json();
                        fetchRaks(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalEditRak')).hide();
                        alert('Updated successfully');
                    } catch (err) {
                        alert('Failed to update: ' + err.message);
                    }
                });

                // Delete
                if ($btnDelete) {
                    $btnDelete.addEventListener('click', async function() {
                        const ids = getSelectedIds();
                        if (!ids.length) return alert('Select raks first');
                        if (!confirm(`Delete ${ids.length} raks?`)) return;
                        try {
                            const res = await fetch('/raks/destroy-selected', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ ids })
                            });
                            if (!res.ok) {
                                const errorData = await res.json();
                                throw new Error(errorData.message || 'Failed to delete');
                            }
                            const data = await res.json();
                            fetchRaks(currentPage);
                            alert('Deleted successfully');
                        } catch (err) {
                            alert('Failed to delete: ' + err.message);
                        }
                    });
                }

                // Refresh
                $btnRefresh.addEventListener('click', () => fetchRaks(currentPage));

                // Search
                $searchInput.addEventListener('input', function() {
                    searchQuery = this.value;
                    fetchRaks(1);
                });

                // Filter toggle
                $btnFilter.addEventListener('click', () => $filterDropdown.classList.toggle('show'));

                // Apply filter
                $btnApplyFilter.addEventListener('click', () => {
                    filterLokasiVal = $filterLokasi.value;
                    filterKategoriVal = $filterKategori.value;
                    fetchRaks(1);
                    $filterDropdown.classList.remove('show');
                });

                // Clear filter
                $btnClearFilter.addEventListener('click', () => {
                    $filterLokasi.value = '';
                    $filterKategori.value = '';
                    filterLokasiVal = '';
                    filterKategoriVal = '';
                    fetchRaks(1);
                    $filterDropdown.classList.remove('show');
                });

                // Pagination - Event delegation for links
                document.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' && e.target.href.includes('page=')) {
                        e.preventDefault();
                        const url = new URL(e.target.href);
                        currentPage = url.searchParams.get('page');
                        fetchRaks(currentPage);
                    }
                });

                // Init
                attachRowHandlers();
                attachDetailHandlers();
                toggleButtons();
            })();
        </script>
    @endpush
@endsection
