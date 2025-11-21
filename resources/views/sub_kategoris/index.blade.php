<!-- resources/views/sub_kategoris/index.blade.php -->

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
                <button id="btn-new-subkategori" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Sub Kategori</span>
                </button>
                <div class="vr"></div>
                <button id="btn-detail-subkategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-book"></i>
                    <span>Detail</span>
                </button>
                <button id="btn-edit-subkategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-pencil"></i>
                    <span>Edit</span>
                </button>
                <button id="btn-delete-subkategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
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
                <input id="search-subkategori" type="text" class="form-control search-input" placeholder="Search by Nama">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
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
            <span id="subkategori-count">{{ $subkategoris->total() }} sub kategoris found</span>
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
                        <th class="py-3 fw-semibold">Kategori</th>
                        <th class="py-3 fw-semibold text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="subkategori-table-body">
                    @include('sub_kategoris.partials.rows')
                    </tbody>
                </table>
            </div>
            @include('sub_kategoris.partials.pagination')
        </div>
    </div>

    @include('sub_kategoris.partials.new-subkategori-modal')
    @include('sub_kategoris.partials.edit-subkategori-modal')
    @include('sub_kategoris.partials.subkategori-detail-modal')
    @include('sub_kategoris.partials.bukus-list-modal')

    @push('scripts')
        <script>
            (function() {
                const csrf = '{{ csrf_token() }}';
                const $selectAll = document.getElementById('select-all');
                const $btnNew = document.getElementById('btn-new-subkategori');
                const $btnDetail = document.getElementById('btn-detail-subkategori');
                const $btnEdit = document.getElementById('btn-edit-subkategori');
                const $btnDelete = document.getElementById('btn-delete-subkategori');
                const $btnRefresh = document.getElementById('btn-refresh');
                const $searchInput = document.getElementById('search-subkategori');
                const $btnFilter = document.getElementById('btn-filter');
                const $filterDropdown = document.getElementById('filter-dropdown');
                const $filterKategori = document.getElementById('filter-kategori');
                const $btnApplyFilter = document.getElementById('btn-apply-filter');
                const $btnClearFilter = document.getElementById('btn-clear-filter');
                const $subkategoriCount = document.getElementById('subkategori-count');
                const $tableBody = document.getElementById('subkategori-table-body');

                let currentPage = 1;
                let searchQuery = '';
                let filterKategoriVal = '';

                // Toggle buttons based on selection
                function toggleButtons() {
                    const selected = getSelectedIds().length;
                    if ($btnDetail) $btnDetail.disabled = selected !== 1;
                    if ($btnEdit) $btnEdit.disabled = selected !== 1;
                    if ($btnDelete) $btnDelete.disabled = selected === 0;
                }

                function getSelectedIds() {
                    return Array.from(document.querySelectorAll('.select-subkategori:checked')).map(el => el.value);
                }

                function attachRowHandlers() {
                    document.querySelectorAll('.select-subkategori').forEach(el => {
                        el.addEventListener('change', toggleButtons);
                    });
                    $selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.select-subkategori').forEach(el => el.checked = this.checked);
                        toggleButtons();
                    });
                }

                function attachDetailHandlers() {
                    document.querySelectorAll('.btn-view-detail').forEach(btn => {
                        btn.addEventListener('click', showSubkategoriDetail);

                        document.querySelectorAll('.btn-view-bukus').forEach(btn => {
                            btn.addEventListener('click', showBukusList);
                        });
                    });
                }

                // Fetch subkategoris with AJAX for partial HTML
                async function fetchSubkategoris(page = 1) {
                    try {
                        const params = new URLSearchParams({
                            page,
                            search: searchQuery,
                            kategori: filterKategoriVal
                        });
                        const res = await fetch(`/sub_kategoris?${params.toString()}`, {
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
                        $subkategoriCount.textContent = document.querySelectorAll('#subkategori-table-body tr:not(.no-data)').length + ' sub kategoris found';
                    } catch (err) {
                        console.error('Failed to fetch subkategoris', err);
                        alert('Failed to fetch subkategoris');
                    }
                }

                // Show subkategori detail with JSON
                async function showSubkategoriDetail(e) {
                    const id = e.target.closest('button').dataset.id;
                    try {
                        const res = await fetch(`/sub_kategoris/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const subkategori = await res.json();
                        document.getElementById('detail-sub-kategori').textContent = subkategori.nama || '-';
                        document.getElementById('detail-kategori').textContent = subkategori.kategori?.nama || '-';
                        new bootstrap.Modal(document.getElementById('modalSubkategoriDetail')).show();
                    } catch (err) {
                        alert('Failed to fetch subkategori detail: ' + err.message);
                    }
                }

                async function showBukusList(e) {
                    const id = e.target.closest('button').dataset.id;
                    try {
                        const res = await fetch(`/sub_kategoris/${id}/bukus`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const bukus = await res.json();
                        const $bukusBody = document.getElementById('bukus-table-body');
                        $bukusBody.innerHTML = '';
                        if (bukus.length === 0) {
                            $bukusBody.innerHTML = '<tr><td colspan="4" class="text-center">No bukus found.</td></tr>';
                        } else {
                            bukus.forEach(buku => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                    <td>${buku.judul}</td>
                    <td>${buku.pengarang}</td>
                    <td>${buku.tahun_terbit}</td>
                    <td>${buku.kategori?.nama ?? '-'}</td>
                `;
                                $bukusBody.appendChild(tr);
                            });
                        }
                        new bootstrap.Modal(document.getElementById('modalBukusList')).show();
                    } catch (err) {
                        alert('Failed to fetch bukus: ' + err.message);
                    }
                }

                // New Subkategori
                if ($btnNew) {
                    $btnNew.addEventListener('click', () => {
                        new bootstrap.Modal(document.getElementById('modalNewSubkategori')).show();
                    });
                }

                // Form submit for new with FormData and proper headers
                document.getElementById('form-new-subkategori').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const res = await fetch('/sub_kategoris', {
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
                        fetchSubkategoris(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalNewSubkategori')).hide();
                        alert('Created successfully');
                    } catch (err) {
                        alert('Failed to create: ' + err.message);
                    }
                });

                // Edit Subkategori
                if ($btnEdit) {
                    $btnEdit.addEventListener('click', async function() {
                        const id = getSelectedIds()[0];
                        try {
                            const res = await fetch(`/sub_kategoris/${id}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (!res.ok) throw new Error('Failed to fetch');
                            const subkategori = await res.json();
                            document.getElementById('edit-subkategori-id').value = subkategori.id;
                            document.getElementById('edit-sub-kategori').value = subkategori.sub_kategori;
                            document.getElementById('edit-id-kategori').value = subkategori.id_kategori;
                            new bootstrap.Modal(document.getElementById('modalEditSubkategori')).show();
                        } catch (err) {
                            alert('Failed to load edit data: ' + err.message);
                        }
                    });
                }

                // Form submit for edit with FormData and proper headers
                document.getElementById('form-edit-subkategori').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit-subkategori-id').value;
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    try {
                        const res = await fetch(`/sub_kategoris/${id}`, {
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
                        fetchSubkategoris(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('modalEditSubkategori')).hide();
                        alert('Updated successfully');
                    } catch (err) {
                        alert('Failed to update: ' + err.message);
                    }
                });

                // Delete
                if ($btnDelete) {
                    $btnDelete.addEventListener('click', async function() {
                        const ids = getSelectedIds();
                        if (!ids.length) return alert('Select sub kategoris first');
                        if (!confirm(`Delete ${ids.length} sub kategoris?`)) return;
                        try {
                            const res = await fetch('/sub_kategoris/destroy-selected', {
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
                            fetchSubkategoris(currentPage);
                            alert('Deleted successfully');
                        } catch (err) {
                            alert('Failed to delete: ' + err.message);
                        }
                    });
                }

                // Refresh
                $btnRefresh.addEventListener('click', () => fetchSubkategoris(currentPage));

                // Search
                $searchInput.addEventListener('input', function() {
                    searchQuery = this.value;
                    fetchSubkategoris(1);
                });

                // Filter toggle
                $btnFilter.addEventListener('click', () => $filterDropdown.classList.toggle('show'));

                // Apply filter
                $btnApplyFilter.addEventListener('click', () => {
                    filterKategoriVal = $filterKategori.value;
                    fetchSubkategoris(1);
                    $filterDropdown.classList.remove('show');
                });

                // Clear filter
                $btnClearFilter.addEventListener('click', () => {
                    $filterKategori.value = '';
                    filterKategoriVal = '';
                    fetchSubkategoris(1);
                    $filterDropdown.classList.remove('show');
                });

                // Pagination - Event delegation for links
                document.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' && e.target.href.includes('page=')) {
                        e.preventDefault();
                        const url = new URL(e.target.href);
                        currentPage = url.searchParams.get('page');
                        fetchSubkategoris(currentPage);
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
