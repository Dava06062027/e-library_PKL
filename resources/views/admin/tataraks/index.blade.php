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
                <button id="btn-bulk-tatarak" class="btn btn-m365 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalBulkTatarak">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tata Buku</span>
                </button>
            <div class="vr"></div>
            <button id="btn-edit-tatarak" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </button>
            <button id="btn-delete-tatarak" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
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
                <input id="search-tatarak" type="text" class="form-control search-input" placeholder="Search by barcode or user">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <!-- Form filter: by rak, role user -->
                    <div class="mb-2">
                        <label>Rak</label>
                        <select id="filter-rak" class="form-select">
                            <option value="">All</option>
                            @foreach(\App\Models\Rak::all() as $rak)
                                <option value="{{ $rak->id }}">{{ $rak->nama }}</option>
                            @endforeach
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
                    <th>Eksemplar Buku</th>
                    <th>Rak</th>
                    <th>Posisi</th>
                    <th>Petugas</th>
                    <th>Modified Date</th>
                </tr>
                </thead>
                <tbody id="tataraks-rows">
                <!-- Rows will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-3"></div>

        <!-- Include Modals -->
        @include('admin.tataraks.partials.new-modal')
        @include('admin.tataraks.partials.edit-modal')
        @include('admin.tataraks.partials.bulk-modal')
        @include('admin.tataraks.partials.select-buku-modal')
    </div>

    @push('scripts')
        <script>
            (() => {
                const $tableBody = document.getElementById('tataraks-rows');
                const $pagination = document.getElementById('pagination');
                const $btnBulk = document.getElementById('btn-bulk-tatarak');
                const $btnEdit = document.getElementById('btn-edit-tatarak');
                const $btnDelete = document.getElementById('btn-delete-tatarak');
                const $btnRefresh = document.getElementById('btn-refresh');
                const $searchInput = document.getElementById('search-tatarak');
                const $btnFilter = document.getElementById('btn-filter');
                const $filterDropdown = document.getElementById('filter-dropdown');
                const $btnApplyFilter = document.getElementById('btn-apply-filter');
                const $selectAll = document.getElementById('select-all');
                const csrf = '{{ csrf_token() }}';
                const copyNotification = document.getElementById('copy-notification');
                let currentFilters = {};

                const showCopyNotification = () => {
                    copyNotification.classList.add('show');
                    setTimeout(() => copyNotification.classList.remove('show'), 2000);
                };

                const getSelectedIds = () => Array.from(document.querySelectorAll('.select-tatarak:checked')).map(cb => cb.value);

                const toggleButtons = () => {
                    const selectedCount = getSelectedIds().length;
                    $btnEdit.disabled = selectedCount !== 1;
                    $btnDelete.disabled = selectedCount === 0;
                };

                const attachRowHandlers = () => {
                    document.querySelectorAll('.select-tatarak').forEach(cb => cb.addEventListener('change', toggleButtons));
                };

                const fetchTataraks = async (filters = {}) => {
                    const url = new URL('{{ route('admin.tataraks.index') }}', window.location.origin);
                    Object.entries(filters).forEach(([key, value]) => {
                        if (value) url.searchParams.append(key, value);
                    });
                    try {
                        const res = await fetch(url.toString(), {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        $tableBody.innerHTML = data.rows;
                        $pagination.innerHTML = data.pagination;
                        attachRowHandlers();
                        toggleButtons();
                    } catch (err) {
                        alert('Error loading data');
                    }
                };

                // Expose to window for bulk script
                window.fetchTataraks = fetchTataraks;
                window.currentFilters = currentFilters;

                $btnRefresh.addEventListener('click', () => fetchTataraks(currentFilters));

                $searchInput.addEventListener('input', (e) => {
                    currentFilters.q = e.target.value;
                    fetchTataraks(currentFilters);
                });

                $btnFilter.addEventListener('click', () => $filterDropdown.classList.toggle('show'));

                $btnApplyFilter.addEventListener('click', () => {
                    currentFilters.rak = document.getElementById('filter-rak').value;
                    currentFilters.role = document.getElementById('filter-role').value;
                    fetchTataraks(currentFilters);
                    $filterDropdown.classList.remove('show');
                });

                $selectAll.addEventListener('change', (e) => {
                    document.querySelectorAll('.select-tatarak').forEach(cb => cb.checked = e.target.checked);
                    toggleButtons();
                });

                document.getElementById('form-edit-tatarak').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit-id').value;
                    const formData = new FormData(this);
                    const data = Object.fromEntries(formData);
                    try {
                        const res = await fetch(`{{ url('admin/tataraks') }}/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });
                        const response = await res.json();
                        if (!res.ok) {
                            const errors = response.errors ? Object.values(response.errors).flat().join('\n') : response.error || response.message;
                            throw new Error(errors);
                        }
                        bootstrap.Modal.getInstance(document.getElementById('modalEditTatarak')).hide();
                        fetchTataraks(currentFilters);
                        alert(response.message || 'Penataan berhasil diupdate');
                    } catch (err) {
                        alert(err.message || 'Error mengupdate penataan');
                    }
                });

                $btnDelete.addEventListener('click', async function() {
                    const ids = getSelectedIds();
                    if (!ids.length) return alert('Pilih penataan terlebih dahulu');
                    if (!confirm(`Hapus ${ids.length} penataan?`)) return;
                    try {
                        const res = await fetch("{{ route('admin.tataraks.destroySelected') }}", {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ids })
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            throw new Error(data.error || data.message || 'Hapus gagal');
                        }
                        fetchTataraks(currentFilters);
                        alert(data.message || 'Penataan berhasil dihapus');
                    } catch (err) {
                        alert(err.message || 'Error menghapus penataan');
                    }
                });

                attachRowHandlers();
                toggleButtons();
                fetchTataraks(); // Initial load

            })();

            // ===== BULK TATARAK SCRIPT (Fixed Routes Version) =====
            $(document).ready(function() {
                // Global storage untuk menyimpan pilihan buku & eksemplar
                let selectedBooksData = {}; // Format: { id_buku: { judul: '', eksemplar: [id1, id2, ...] } }
                let currentBukuId = null;
                let bukuTable = null;

                // Base URL untuk API calls
                const BASE_URL = '{{ url('admin/tataraks') }}';

                // Helper function: Update tampilan daftar buku terpilih di bulk modal
                function updateSelectedBooksDisplay() {
                    const container = $('#selected-books-container');
                    const list = $('#selected-books-list');

                    const bukuIds = Object.keys(selectedBooksData);
                    if (bukuIds.length === 0) {
                        container.hide();
                        return;
                    }

                    container.show();
                    let html = '';
                    let totalEksemplar = 0;

                    bukuIds.forEach(bukuId => {
                        const data = selectedBooksData[bukuId];
                        totalEksemplar += data.eksemplar.length;

                        html += `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${data.judul}</h6>
                                <small class="text-muted">${data.eksemplar.length} eksemplar dipilih</small>
                                <div class="mt-2">
                                    ${data.eksemplarDetails.map(e => `
                                        <span class="badge bg-secondary me-1">${e.barcode}</span>
                                    `).join('')}
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-buku" data-buku-id="${bukuId}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
                    });

                    list.html(html);
                    $('#total-eksemplar-count').text(totalEksemplar);
                }

                // Remove buku dari pilihan
                $(document).on('click', '.btn-remove-buku', function() {
                    const bukuId = $(this).data('buku-id');
                    delete selectedBooksData[bukuId];
                    updateSelectedBooksDisplay();
                });

                // Init DataTable untuk daftar buku
                $('#modalSelectBuku').on('shown.bs.modal', function () {
                    // Show section judul, hide section eksemplar
                    $('#section-pilih-judul').show();
                    $('#section-pilih-eksemplar').hide();

                    if (!bukuTable) {
                        bukuTable = $('#buku-table').DataTable({
                            ajax: '{{ route('admin.tataraks.searchBukuDatatable') }}',
                            serverSide: true,
                            processing: true,
                            columns: [
                                { data: 'id' },
                                { data: 'judul' },
                                { data: 'pengarang' },
                                { data: 'tahun_terbit' },
                                { data: 'eksemplar_tersedia' },
                                {
                                    data: null,
                                    orderable: false,
                                    render: function (data) {
                                        return `<button class="btn btn-primary btn-sm btn-pilih-buku" data-id="${data.id}" data-judul="${data.judul}">Pilih</button>`;
                                    }
                                }
                            ],
                            searching: false,
                            paging: true,
                            pageLength: 10,
                        });
                    }
                });

                // Search manual
                $('#search-buku-modal').on('input', debounce(function () {
                    if (bukuTable) {
                        bukuTable.search(this.value).draw();
                    }
                }, 300));

                // Filter tahun
                $('#btn-apply-filter-buku').on('click', function () {
                    const tahun = $('#filter-tahun').val();
                    if (bukuTable) {
                        bukuTable.column(3).search(tahun ? '^' + tahun + '$' : '', true, false).draw();
                    }
                    $('#filter-dropdown-buku').removeClass('show');
                });

                $('#btn-filter-buku').on('click', function () {
                    $('#filter-dropdown-buku').toggleClass('show');
                });

                // Handle pilih buku (load eksemplar)
                $(document).on('click', '.btn-pilih-buku', async function () {
                    const bukuId = $(this).data('id');
                    const judul = $(this).data('judul');

                    currentBukuId = bukuId;
                    $('#selected-judul-text').text(judul);

                    // Hide section judul, show section eksemplar
                    $('#section-pilih-judul').hide();
                    $('#section-pilih-eksemplar').show();

                    // Load eksemplar yang tersedia
                    try {
                        const url = `${BASE_URL}/available-eksemplar/${bukuId}`;
                        const res = await fetch(url);

                        if (!res.ok) {
                            throw new Error('Failed to load eksemplar');
                        }

                        const eksemplarList = await res.json();

                        const tbody = $('#eksemplar-table-body');
                        tbody.empty();

                        if (eksemplarList.length === 0) {
                            tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada eksemplar tersedia</td></tr>');
                            return;
                        }

                        eksemplarList.forEach(eks => {
                            tbody.append(`
                    <tr>
                        <td><input type="checkbox" class="form-check-input select-eksemplar" value="${eks.id}" data-barcode="${eks.barcode}" data-kondisi="${eks.kondisi}" data-status="${eks.status}" data-sumber="${eks.sumber}"></td>
                        <td>${eks.barcode}</td>
                        <td>${eks.kondisi}</td>
                        <td>${eks.status}</td>
                        <td>${eks.sumber}</td>
                    </tr>
                `);
                        });

                        updateSelectedEksemplarCount();
                    } catch (err) {
                        console.error('Error:', err);
                        alert('Error loading eksemplar: ' + err.message);
                    }
                });

                // Back to judul list
                $('#btn-back-to-judul').on('click', function() {
                    $('#section-pilih-eksemplar').hide();
                    $('#section-pilih-judul').show();
                    currentBukuId = null;
                });

                // Select all eksemplar
                $('#select-all-eksemplar').on('change', function() {
                    $('.select-eksemplar').prop('checked', this.checked);
                    updateSelectedEksemplarCount();
                });

                // Update count saat checkbox berubah
                $(document).on('change', '.select-eksemplar', function() {
                    updateSelectedEksemplarCount();
                });

                function updateSelectedEksemplarCount() {
                    const count = $('.select-eksemplar:checked').length;
                    $('#selected-eksemplar-count').text(count);
                }

                // Apply range barcode
                $('#btn-apply-range').on('click', function() {
                    const rangeInput = $('#range-barcode').val().trim();
                    if (!rangeInput) return alert('Masukkan range barcode!');

                    const parts = rangeInput.split('-');
                    if (parts.length !== 2) return alert('Format salah! Gunakan: BARCODE_AWAL-BARCODE_AKHIR');

                    const start = parts[0].trim();
                    const end = parts[1].trim();

                    // Uncheck all first
                    $('.select-eksemplar').prop('checked', false);

                    // Check yang masuk range
                    let found = false;
                    $('.select-eksemplar').each(function() {
                        const barcode = $(this).data('barcode');
                        if (barcode >= start && barcode <= end) {
                            $(this).prop('checked', true);
                            found = true;
                        }
                    });

                    if (!found) alert('Tidak ada barcode dalam range tersebut');
                    updateSelectedEksemplarCount();
                });

                // Konfirmasi pilihan eksemplar
                $('#btn-confirm-eksemplar').on('click', async function() {
                    const selectedEks = [];
                    const selectedEksDetails = [];

                    $('.select-eksemplar:checked').each(function() {
                        selectedEks.push(parseInt($(this).val()));
                        selectedEksDetails.push({
                            id: parseInt($(this).val()),
                            barcode: $(this).data('barcode'),
                            kondisi: $(this).data('kondisi'),
                            status: $(this).data('status'),
                            sumber: $(this).data('sumber')
                        });
                    });

                    if (selectedEks.length === 0) {
                        return alert('Pilih minimal 1 eksemplar!');
                    }

                    // Ambil kategori buku untuk filter rak
                    let kategoriId = null;
                    try {
                        const url = `${BASE_URL}/buku-kategori/${currentBukuId}`;
                        const res = await fetch(url);

                        if (res.ok) {
                            const data = await res.json();
                            kategoriId = data.id_kategori;
                        }
                    } catch(err) {
                        console.error('Error getting kategori:', err);
                    }

                    // Simpan ke storage
                    const judul = $('#selected-judul-text').text();
                    selectedBooksData[currentBukuId] = {
                        judul: judul,
                        eksemplar: selectedEks,
                        eksemplarDetails: selectedEksDetails,
                        kategoriId: kategoriId
                    };

                    // Update tampilan di bulk modal
                    updateSelectedBooksDisplay();

                    // Update dropdown rak berdasarkan kategori
                    await updateRakDropdown();

                    // Close select modal, back to bulk modal
                    $('#modalSelectBuku').modal('hide');
                    $('#modalBulkTatarak').modal('show');

                    // Reset section
                    $('#section-pilih-eksemplar').hide();
                    $('#section-pilih-judul').show();
                    currentBukuId = null;
                });

                // Function untuk update dropdown rak berdasarkan kategori buku yang dipilih
                async function updateRakDropdown() {
                    // Ambil semua kategori dari buku yang dipilih
                    const kategoriIds = new Set();
                    Object.values(selectedBooksData).forEach(data => {
                        if (data.kategoriId) {
                            kategoriIds.add(data.kategoriId);
                        }
                    });

                    // Jika tidak ada buku terpilih, kosongkan dropdown
                    if (kategoriIds.size === 0) {
                        const select = $('#select-rak');
                        select.empty();
                        select.append('<option value="">-- Pilih Buku Terlebih Dahulu --</option>');
                        return;
                    }

                    // Jika ada multiple kategori, cek apakah sama semua
                    if (kategoriIds.size > 1) {
                        alert('Peringatan: Buku yang dipilih memiliki kategori berbeda. Pilih rak yang sesuai dengan hati-hati.');
                    }

                    // Ambil rak berdasarkan kategori
                    const kategoriArray = Array.from(kategoriIds);
                    try {
                        const url = `${BASE_URL}/rak-by-kategori?kategoris=${kategoriArray.join(',')}`;
                        const res = await fetch(url);

                        if (!res.ok) {
                            throw new Error('Failed to load rak');
                        }

                        const raks = await res.json();
                        populateRakDropdown(raks);
                    } catch(err) {
                        console.error('Error loading filtered rak:', err);
                        alert('Error loading rak: ' + err.message);
                    }
                }

                // Function untuk populate dropdown rak
                function populateRakDropdown(raks) {
                    const select = $('#select-rak');
                    select.empty();
                    select.append('<option value="">-- Pilih Rak --</option>');

                    if (raks.length === 0) {
                        select.append('<option value="" disabled>Tidak ada rak tersedia untuk kategori ini</option>');
                        return;
                    }

                    raks.forEach(rak => {
                        select.append(`
                <option value="${rak.id}">
                    ${rak.nama} (Kapasitas: ${rak.kapasitas}, ${rak.kolom}x${rak.baris}) - ${rak.kategori_nama}
                </option>
            `);
                    });
                }

                // Submit bulk form
                $('#form-bulk-tatarak').on('submit', async function(e) {
                    e.preventDefault();

                    // Kumpulkan semua eksemplar dari semua buku terpilih
                    const allEksemplarIds = [];
                    Object.values(selectedBooksData).forEach(data => {
                        allEksemplarIds.push(...data.eksemplar);
                    });

                    if (allEksemplarIds.length === 0) {
                        return alert('Pilih minimal 1 eksemplar dari buku!');
                    }

                    const idRak = $('#select-rak').val();
                    if (!idRak) return alert('Pilih rak!');

                    // Generate positions (sequential)
                    const positions = [];
                    for (let i = 0; i < allEksemplarIds.length; i++) {
                        positions.push({
                            kolom: (i % 5) + 1,  // Asumsi 5 kolom per baris
                            baris: Math.floor(i / 5) + 1
                        });
                    }

                    try {
                        const res = await fetch('{{ route('admin.tataraks.bulkStore') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id_buku_items: allEksemplarIds,
                                id_rak: idRak,
                                positions: positions
                            })
                        });

                        const data = await res.json();

                        if (!res.ok) throw new Error(data.error || data.message || 'Failed');

                        $('#modalBulkTatarak').modal('hide');

                        if (typeof window.fetchTataraks === 'function') {
                            window.fetchTataraks(window.currentFilters || {});
                        }

                        alert(data.message || 'Bulk penataan berhasil!');

                        // Reset
                        selectedBooksData = {};
                        updateSelectedBooksDisplay();
                        $('#form-bulk-tatarak')[0].reset();

                    } catch (err) {
                        console.error('Submit error:', err);
                        alert(err.message || 'Error bulk insert');
                    }
                });

                // Reset on modal close
                $('#modalBulkTatarak').on('hidden.bs.modal', function() {
                    // Jangan reset selectedBooksData, biar user bisa kembali edit
                });

                // Debounce helper
                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }
            });
        </script>


    @endpush
@endsection
