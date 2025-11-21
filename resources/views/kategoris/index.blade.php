<!-- resources/views/kategoris/index.blade.php -->

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
                <button id="btn-new-kategori" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Kategori</span>
                </button>
                <div class="vr"></div>
                <button id="btn-detail-kategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-book"></i>
                    <span>Detail</span>
                </button>
                <button id="btn-edit-kategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                    <i class="bi bi-pencil"></i>
                    <span>Edit</span>
                </button>
                <button id="btn-delete-kategori" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
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
                <input id="search-kategori" type="text" class="form-control search-input" placeholder="Search by Nama">
            </div>
            <!-- No complex filters needed for simple table, but can add if wanted -->
        </div>

        <!-- Count -->
        <div class="text-secondary small mb-3">
            <span id="kategori-count">{{ $kategoris->total() }} kategoris found</span>
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
                        <th class="py-3 fw-semibold text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="kategori-table-body">
                    @include('kategoris.partials.rows')
                    </tbody>
                </table>
            </div>
            @include('kategoris.partials.pagination')
        </div>
    </div>

    @include('kategoris.partials.new-kategori-modal')
    @include('kategoris.partials.edit-kategori-modal')
    @include('kategoris.partials.kategori-detail-modal')
    @include('kategoris.partials.subkategoris-list-modal')

    @push('scripts')
    <script>
        (function() {
            const csrf = '{{ csrf_token() }}';
            const $selectAll = document.getElementById('select-all');
            const $btnNew = document.getElementById('btn-new-kategori');
            const $btnDetail = document.getElementById('btn-detail-kategori');
            const $btnEdit = document.getElementById('btn-edit-kategori');
            const $btnDelete = document.getElementById('btn-delete-kategori');
            const $btnRefresh = document.getElementById('btn-refresh');
            const $searchInput = document.getElementById('search-kategori');
            const $kategoriCount = document.getElementById('kategori-count');
            const $tableBody = document.getElementById('kategori-table-body');

            let currentPage = 1;
            let searchQuery = '';

            // Toggle buttons based on selection
            function toggleButtons() {
                const selected = getSelectedIds().length;
                if ($btnDetail) $btnDetail.disabled = selected !== 1;
                if ($btnEdit) $btnEdit.disabled = selected !== 1;
                if ($btnDelete) $btnDelete.disabled = selected === 0;
            }

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.select-kategori:checked')).map(el => el.value);
            }

            function attachRowHandlers() {
                document.querySelectorAll('.select-kategori').forEach(el => {
                    el.addEventListener('change', toggleButtons);
                });
                $selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.select-kategori').forEach(el => el.checked = this.checked);
                    toggleButtons();
                });
            }

            function attachDetailHandlers() {
                document.querySelectorAll('.btn-view-detail').forEach(btn => {
                    btn.addEventListener('click', showKategoriDetail);
                });
                document.querySelectorAll('.btn-view-subkategoris').forEach(btn => {
                    btn.addEventListener('click', showSubkategorisList);
                });
            }

            // Fetch kategoris with AJAX for partial HTML
            async function fetchKategoris(page = 1) {
                try {
                    const params = new URLSearchParams({
                        page,
                        search: searchQuery
                    });
                    const res = await fetch(`/kategoris?${params.toString()}`, {
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
                    $kategoriCount.textContent = document.querySelectorAll('#kategori-table-body tr:not(.no-data)').length + ' kategoris found';
                } catch (err) {
                    console.error('Failed to fetch kategoris', err);
                    alert('Failed to fetch kategoris');
                }
            }

            // Show kategori detail with JSON
            async function showKategoriDetail(e) {
                const id = e.target.closest('button').dataset.id;
                try {
                    const res = await fetch(`/kategoris/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('Failed to fetch');
                    const kategori = await res.json();
                    document.getElementById('detail-nama').textContent = kategori.nama || '-';
                    new bootstrap.Modal(document.getElementById('modalKategoriDetail')).show();
                } catch (err) {
                    alert('Failed to fetch kategori detail: ' + err.message);
                }
            }

            async function showSubkategorisList(e) {
                const id = e.target.closest('button').dataset.id;
                try {
                    const res = await fetch(`/kategoris/${id}/subkategoris`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('Failed to fetch');
                    const subkategoris = await res.json();
                    const $subkategorisBody = document.getElementById('subkategoris-table-body');
                    $subkategorisBody.innerHTML = '';
                    if (subkategoris.length === 0) {
                        $subkategorisBody.innerHTML = '<tr><td class="text-center">No sub kategoris found.</td></tr>';
                    } else {
                        subkategoris.forEach(subkategori => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `<td>${subkategori.nama}</td>`;
                            $subkategorisBody.appendChild(tr);
                        });
                    }
                    new bootstrap.Modal(document.getElementById('modalSubkategorisList')).show();
                } catch (err) {
                    alert('Failed to fetch sub kategoris: ' + err.message);
                }
            }

            // New Kategori
            if ($btnNew) {
                $btnNew.addEventListener('click', () => {
                    new bootstrap.Modal(document.getElementById('modalNewKategori')).show();
                });
            }

            // Form submit for new with FormData and proper headers
            document.getElementById('form-new-kategori').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                try {
                    const res = await fetch('/kategoris', {
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
                    fetchKategoris(currentPage);
                    bootstrap.Modal.getInstance(document.getElementById('modalNewKategori')).hide();
                    alert('Created successfully');
                } catch (err) {
                    alert('Failed to create: ' + err.message);
                }
            });

            // Edit Kategori
            if ($btnEdit) {
                $btnEdit.addEventListener('click', async function() {
                    const id = getSelectedIds()[0];
                    try {
                        const res = await fetch(`/kategoris/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('Failed to fetch');
                        const kategori = await res.json();
                        document.getElementById('edit-kategori-id').value = kategori.id;
                        document.getElementById('edit-nama').value = kategori.nama;
                        new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
                    } catch (err) {
                        alert('Failed to load edit data: ' + err.message);
                    }
                });
            }

            // Form submit for edit with FormData and proper headers
            document.getElementById('form-edit-kategori').addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = document.getElementById('edit-kategori-id').value;
                const formData = new FormData(this);
                formData.append('_method', 'PUT');
                try {
                    const res = await fetch(`/kategoris/${id}`, {
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
                    fetchKategoris(currentPage);
                    bootstrap.Modal.getInstance(document.getElementById('modalEditKategori')).hide();
                    alert('Updated successfully');
                } catch (err) {
                    alert('Failed to update: ' + err.message);
                }
            });

            // Delete
            if ($btnDelete) {
                $btnDelete.addEventListener('click', async function() {
                    const ids = getSelectedIds();
                    if (!ids.length) return alert('Select kategoris first');
                    if (!confirm(`Delete ${ids.length} kategoris?`)) return;
                    try {
                        const res = await fetch('/kategoris/destroy-selected', {
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
                        fetchKategoris(currentPage);
                        alert('Deleted successfully');
                    } catch (err) {
                        alert('Failed to delete: ' + err.message);
                    }
                });
            }

            // Refresh
            $btnRefresh.addEventListener('click', () => fetchKategoris(currentPage));

            // Search
            $searchInput.addEventListener('input', function() {
                searchQuery = this.value;
                fetchKategoris(1);
            });

            // Pagination - Event delegation for links
            document.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' && e.target.href.includes('page=')) {
                    e.preventDefault();
                    const url = new URL(e.target.href);
                    currentPage = url.searchParams.get('page');
                    fetchKategoris(currentPage);
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
