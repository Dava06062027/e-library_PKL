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
        }
        .btn-m365:hover:not(:disabled) {
            background-color: #e8e8e8 !important;
            color: #323130;
        }
        .btn-m365:disabled {
            color: #a19f9d;
            cursor: not-allowed;
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
            min-width: 250px;
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
                <input id="search-peminjaman" type="text" class="form-control search-input" placeholder="Search by member, judul, barcode, petugas">
            </div>
            <div class="position-relative">
                <button id="btn-filter" type="button" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" id="filter-tanggal" class="form-control">
                        <small class="text-muted">Transaksi di tanggal ini (pinjam/due/kembali)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Keterlambatan</label>
                        <select id="filter-keterlambatan" class="form-select">
                            <option value="">All</option>
                            <option value="tepat_waktu">Tepat Waktu</option>
                            <option value="telat">Telat (Melewati Due Date)</option>
                        </select>
                        <small class="text-muted">Hanya untuk status "Dipinjam"</small>
                    </div>
                    <button id="btn-apply-filter" type="button" class="btn btn-primary w-100">Apply</button>
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
                    <th>Judul Buku</th>
                    <th>Barcode</th>
                    <th>Tanggal Pinjam</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Hari Telat</th>
                    <th>Total Denda</th>
                    <th>Petugas</th>
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

@endsection

@push('scripts')
    <script>
        (() => {
            const BASE_URL = '{{ route('admin.peminjamans.index') }}';
            const $tableBody = document.getElementById('peminjamans-rows');
            const $pagination = document.getElementById('pagination');
            const $btnReturn = document.getElementById('btn-return-peminjaman');
            const $btnExtend = document.getElementById('btn-extend-peminjaman');
            const $btnDelete = document.getElementById('btn-delete-peminjaman');
            const csrf = '{{ csrf_token() }}';

            let currentPage = 1;
            let currentFilters = {};
            let selectedPeminjamans = [];


            async function fetchPeminjamans(filters = {}) {
                try {
                    const params = new URLSearchParams({
                        page: currentPage,
                        ...filters
                    });
                    const res = await fetch(`${BASE_URL}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await res.text();
                    $tableBody.innerHTML = html;
                    attachCheckboxListeners();
                    updateButtons();
                } catch (err) {
                    console.error('Fetch error:', err);
                }
            }


            document.getElementById('btn-refresh').addEventListener('click', () => {
                location.reload();
            });


            document.getElementById('search-peminjaman').addEventListener('input', debounce((e) => {
                currentFilters.search = e.target.value;
                fetchPeminjamans(currentFilters);
            }, 300));


            document.getElementById('btn-filter').addEventListener('click', (e) => {
                e.stopPropagation();
                document.getElementById('filter-dropdown').classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                const dropdown = document.getElementById('filter-dropdown');
                const btnFilter = document.getElementById('btn-filter');
                if (!dropdown.contains(e.target) && !btnFilter.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });

            document.getElementById('btn-apply-filter').addEventListener('click', () => {
                currentFilters.status = document.getElementById('filter-status').value;
                currentFilters.tanggal = document.getElementById('filter-tanggal').value;
                currentFilters.keterlambatan = document.getElementById('filter-keterlambatan').value;
                document.getElementById('filter-dropdown').classList.remove('show');
                fetchPeminjamans(currentFilters);
            });


            document.getElementById('select-all').addEventListener('change', (e) => {
                document.querySelectorAll('.select-peminjaman').forEach(cb => cb.checked = e.target.checked);
                updateSelected();
            });

            function attachCheckboxListeners() {
                document.querySelectorAll('.select-peminjaman').forEach(cb => {
                    cb.addEventListener('change', updateSelected);
                });
            }

            function updateSelected() {
                selectedPeminjamans = Array.from(document.querySelectorAll('.select-peminjaman:checked')).map(cb => ({
                    id: cb.value,
                    status: cb.dataset.status,
                    memberName: cb.dataset.memberName,
                    returnDate: cb.dataset.returnDate,
                    officerName: cb.dataset.officerName,
                    perpanjanganCount: parseInt(cb.dataset.perpanjanganCount || 0)
                }));
                updateButtons();
            }

            function updateButtons() {
                const hasSelection = selectedPeminjamans.length > 0;
                $btnReturn.disabled = !hasSelection;
                $btnExtend.disabled = !hasSelection;
                $btnDelete.disabled = !hasSelection;
            }


            $btnReturn.addEventListener('click', async () => {
                if (selectedPeminjamans.length !== 1) {
                    alert('Pilih 1 peminjaman untuk dikembalikan!');
                    return;
                }

                const selected = selectedPeminjamans[0];


                if (selected.status === 'Dikembalikan') {
                    alert(`Buku telah dikembalikan oleh ${selected.memberName} pada ${selected.returnDate} yang dilayani oleh ${selected.officerName}`);
                    return;
                }

                document.getElementById('return-id-peminjaman').value = selected.id;
                new bootstrap.Modal(document.getElementById('modalReturnPeminjaman')).show();
            });


            $btnExtend.addEventListener('click', async () => {
                if (selectedPeminjamans.length !== 1) {
                    alert('Pilih 1 peminjaman untuk diperpanjang!');
                    return;
                }

                const selected = selectedPeminjamans[0];


                if (selected.status === 'Dikembalikan') {
                    alert(`Buku telah dikembalikan oleh ${selected.memberName} pada ${selected.returnDate} yang dilayani oleh ${selected.officerName}`);
                    return;
                }


                if (selected.perpanjanganCount >= 1) {
                    alert('Peminjaman ini sudah diperpanjang 1x. Tidak bisa perpanjang lagi pada periode ini.');
                    return;
                }


                document.getElementById('extend-id-peminjaman').value = selected.id;


                try {
                    const res = await fetch(`{{ url('admin/peminjamans') }}/${selected.id}`);
                    const peminjaman = await res.json();

                    const dueDate = new Date(peminjaman.tanggal_kembali_rencana);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    dueDate.setHours(0, 0, 0, 0);

                    const diffTime = today - dueDate;
                    const daysLate = Math.max(0, Math.floor(diffTime / (1000 * 60 * 60 * 24)));
                    const totalDenda = daysLate * 1000;

                    $('#extend-due-date-lama').text(peminjaman.tanggal_kembali_rencana);
                    $('#extend-hari-telat').text(daysLate > 0 ? `${daysLate} hari` : '0 hari (Tepat Waktu)');
                    $('#extend-denda-telat').text(`Rp ${totalDenda.toLocaleString('id-ID')}`);
                    $('#extend-biaya').val(totalDenda);

                    const minDate = new Date();
                    minDate.setDate(minDate.getDate() + 1);
                    const maxDate = new Date(dueDate);
                    maxDate.setDate(maxDate.getDate() + 5);

                    $('#extend-tanggal-baru').attr('min', minDate.toISOString().split('T')[0]);
                    $('#extend-tanggal-baru').attr('max', maxDate.toISOString().split('T')[0]);
                    $('#extend-tanggal-baru').val(maxDate.toISOString().split('T')[0]);

                    new bootstrap.Modal(document.getElementById('modalExtendPeminjaman')).show();

                } catch (err) {
                    console.error('Fetch peminjaman error:', err);
                    alert('Error loading peminjaman data');
                }
            });


            $btnDelete.addEventListener('click', async () => {
                const ids = selectedPeminjamans.map(p => p.id);
                if (!confirm(`Yakin hapus ${ids.length} peminjaman?`)) return;

                try {
                    const res = await fetch('{{ route("admin.peminjamans.destroySelected") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type': 'application/json',
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        body: JSON.stringify({ ids })
                    });

                    if (res.ok) {
                        location.reload();
                    } else {
                        alert('Gagal menghapus');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error: ' + err.message);
                }
            });

            attachCheckboxListeners();

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
        })();
    </script>


    @include('admin.peminjamans.partials.select-scripts')
@endpush
