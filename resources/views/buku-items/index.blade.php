<!-- resources/views/buku-items/index.blade.php -->

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
                <button id="btn-new-item" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Item</span>
                </button>
                <div class="vr"></div>
                <button id="btn-detail-item" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-book"></i>
                    <span>Detail</span>
                </button>
                <button id="btn-edit-item" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-pencil"></i>
                    <span>Edit</span>
                </button>
                <button id="btn-delete-item" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
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
                <input id="search-item" type="text" class="form-control search-input" placeholder="Search by Barcode">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Kondisi</label>
                        <select id="filter-kondisi" class="form-select form-select-sm">
                            <option value="">All Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Status</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Reparasi">Reparasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Buku</label>
                        <select id="filter-buku" class="form-select form-select-sm">
                            <option value="">All Buku</option>
                            @foreach($bukus as $buku)
                                <option value="{{ $buku->id }}">{{ $buku->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Rak</label>
                        <select id="filter-rak" class="form-select form-select-sm">
                            <option value="">All Rak</option>
                            @foreach($raks as $rak)
                                <option value="{{ $rak->id }}">{{ $rak->nama }}</option>
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
            <span id="item-count">{{ $items->total() }} items found</span>
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
                        <th class="py-3 fw-semibold">Barcode</th>
                        <th class="py-3 fw-semibold">Kondisi</th>
                        <th class="py-3 fw-semibold">Status</th>
                        <th class="py-3 fw-semibold">Sumber</th>
                        <th class="py-3 fw-semibold">Buku</th>
                        <th class="py-3 fw-semibold">Rak</th>
                        <th class="py-3 fw-semibold text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="item-table-body">
                    @include('buku-items.partials.rows')
                    </tbody>
                </table>
            </div>
            @include('buku-items.partials.pagination')
        </div>
    </div>

    @include('buku-items.partials.new-item-modal')
    @include('buku-items.partials.edit-item-modal')
    @include('buku-items.partials.item-detail-modal')

    @push('scripts')
        <script>
            (function() {
                const csrf = '{{ csrf_token() }}';
                const $selectAll = document.getElementById('select-all');
                const $btnNew = document.getElementById('btn-new-item');
                const $btnDetail = document.getElementById('btn-detail-item');
                const $btnEdit = document.getElementById('btn-edit-item');
                const $btnDelete = document.getElementById('btn-delete-item');
                const $btnRefresh = document.getElementById('btn-refresh');
                const $searchInput = document.getElementById('search-item');
                const $btnFilter = document.getElementById('btn-filter');
                const $filterDropdown = document.getElementById('filter-dropdown');
                const $filterKondisi = document.getElementById('filter-kondisi');
                const $filterStatus = document.getElementById('filter-status');
                const $filterBuku = document.getElementById('filter-buku');
                const $filterRak = document.getElementById('filter-rak');
                const $btnApplyFilter = document.getElementById('btn-apply-filter');
                const $btnClearFilter = document.getElementById('btn-clear-filter');
                const $itemCount = document.getElementById('item-count');
                const $tableBody = document.getElementById('item-table-body');

                let currentPage = 1;
                let searchQuery = '';
                let filterKondisiVal = '';
                let filterStatusVal = '';
                let filterBukuVal = '';
                let filterRakVal = '';

                // Toggle buttons based on selection
                function toggleButtons() {
                    const selected = getSelectedIds().length;
                    if ($btnDetail) $btnDetail.disabled = selected !== 1;
                    if ($btnEdit) $btnEdit.disabled = selected !== 1;
                    if ($btnDelete) $btnDelete.disabled = selected === 0;
                }

                function getSelectedIds() {
                    return Array.from(document.querySelectorAll('.select-item:checked')).map(el => el.value);
                }

                function attachRowHandlers() {
                    document.querySelectorAll('.select-item').forEach(el => {
                        el.addEventListener('change', toggleButtons);
                    });
                    $selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.select-item').forEach(el => el.checked = this.checked);
                        toggleButtons();
                    });
                }

                function attachDetailHandlers() {
                    document.querySelectorAll('.btn-view-detail').forEach(btn => {
                        btn.addEventListener('click', showItemDetail);
                    });
                }

                // Fetch items with AJAX for partial HTML
                async function fetchItems(page = 1) {
                    try {
                        const params = new URLSearchParams({
                            page,
                            search: searchQuery,
                            kondisi: filterKondisiVal,
                            status: filterStatusVal,
                            buku: filterBukuVal,
                            rak: filterRakVal
                        });
                        const res = await fetch(`/buku-items?${params.toString()}`, {
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
                        $itemCount.textContent = document.querySelectorAll('#item-table-body tr:not(.no-data)').length + ' items found';
                    } catch (err) {
                        console.error('Failed to fetch items', err);
                        alert('Failed to fetch items');
                    }
                }

                // Show item detail with JSON
                async function showItemDetail(e) {
                    const id = e.target.closest('button').dataset.id;
                    try {
                        const res = await fetch(`/buku-items/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const item = await res.json();
                        document.getElementById('detail-barcode').textContent = item.barcode || '-';
                        document.getElementById('detail-kondisi').textContent = item.kondisi || '-';
                        document.getElementById('detail-status').textContent = item.status || '-';
                        document.getElementById('detail-sumber').textContent = item.sumber || '-';
                        document.getElementById('detail-buku').textContent = item.buku?.judul || '-';
                        document.getElementById('detail-rak').textContent = item.rak?.nama || '-';
                        new bootstrap.Modal(document.getElementById('modalItemDetail')).show();
                    } catch (err) {
                        alert('Failed to fetch item detail: ' + err.message);
                    }
                }

                // New Item
                if ($btnNew) {
                    $btnNew.addEventListener('click', () => {
                        new bootstrap.Modal(document.getElementById('modalNewItem')).show();
                    });
                }

                // Form submit for new with FormData and proper headers
                document.getElementById('form-new-item').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const res = await fetch('/buku-items', {
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
                        fetchItems(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalNewItem')).hide();
                        alert('Created successfully');
                    } catch (err) {
                        alert('Failed to create: ' + err.message);
                    }
                });

                // Edit Item
                if ($btnEdit) {
                    $btnEdit.addEventListener('click', async function() {
                        const id = getSelectedIds()[0];
                        try {
                            const res = await fetch(`/buku-items/${id}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (!res.ok) throw new Error('Failed to fetch');
                            const item = await res.json();
                            document.getElementById('edit-item-id').value = item.id;
                            const editBuku = document.getElementById('edit-id-buku');
                            if (editBuku) editBuku.value = item.id_buku;
                            const editKondisi = document.getElementById('edit-kondisi');
                            if (editKondisi) editKondisi.value = item.kondisi;
                            const editStatus = document.getElementById('edit-status');
                            if (editStatus) editStatus.value = item.status;
                            const editSumber = document.getElementById('edit-sumber');
                            if (editSumber) editSumber.value = item.sumber;
                            const editRak = document.getElementById('edit-id-rak');
                            if (editRak) editRak.value = item.id_rak || '';
                            // Removed editBarcode line
                            new bootstrap.Modal(document.getElementById('modalEditItem')).show();
                        } catch (err) {
                            alert('Failed to load edit data: ' + err.message);
                        }
                    });
                }

                // Form submit for edit with FormData and proper headers
                document.getElementById('form-edit-item').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit-item-id').value;
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    try {
                        const res = await fetch(`/buku-items/${id}`, {
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
                        fetchItems(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalEditItem')).hide();
                        alert('Updated successfully');
                    } catch (err) {
                        alert('Failed to update: ' + err.message);
                    }
                });

                // Delete
                if ($btnDelete) {
                    $btnDelete.addEventListener('click', async function() {
                        const ids = getSelectedIds();
                        if (!ids.length) return alert('Select items first');
                        if (!confirm(`Delete ${ids.length} items?`)) return;
                        try {
                            const res = await fetch('/buku-items/destroy-selected', {
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
                            fetchItems(currentPage);
                            alert('Deleted successfully');
                        } catch (err) {
                            alert('Failed to delete: ' + err.message);
                        }
                    });
                }

                // Refresh
                $btnRefresh.addEventListener('click', () => fetchItems(currentPage));

                // Search
                $searchInput.addEventListener('input', function() {
                    searchQuery = this.value;
                    fetchItems(1);
                });

                // Filter toggle
                $btnFilter.addEventListener('click', () => $filterDropdown.classList.toggle('show'));

                // Apply filter
                $btnApplyFilter.addEventListener('click', () => {
                    filterKondisiVal = $filterKondisi.value;
                    filterStatusVal = $filterStatus.value;
                    filterBukuVal = $filterBuku.value;
                    filterRakVal = $filterRak.value;
                    fetchItems(1);
                    $filterDropdown.classList.remove('show');
                });

                // Clear filter
                $btnClearFilter.addEventListener('click', () => {
                    $filterKondisi.value = '';
                    $filterStatus.value = '';
                    $filterBuku.value = '';
                    $filterRak.value = '';
                    filterKondisiVal = '';
                    filterStatusVal = '';
                    filterBukuVal = '';
                    filterRakVal = '';
                    fetchItems(1);
                    $filterDropdown.classList.remove('show');
                });

                // Pagination - Event delegation for links
                document.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' && e.target.href.includes('page=')) {
                        e.preventDefault();
                        const url = new URL(e.target.href);
                        currentPage = url.searchParams.get('page');
                        fetchItems(currentPage);
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
