@extends('layouts.app')

@section('content')
    <style>
        /* Microsoft 365 Style */
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
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
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
    </style>

    <div class="bg-m365-white min-vh-100 p-4">
        <!-- Toolbar -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <button id="btn-review" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-eye"></i>
                <span>Review</span>
            </button>
            <button id="btn-approve" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-check-circle"></i>
                <span>Approve</span>
            </button>
            <button id="btn-reject" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-x-circle"></i>
                <span>Reject</span>
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
                <input id="search-registration" type="text" class="form-control search-input" placeholder="Search">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Filter Status</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status Pendaftaran</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="pending_verification">Pending Verifikasi</option>
                            <option value="email_verified">Email Terverifikasi</option>
                            <option value="under_review">Dalam Review</option>
                            <option value="document_requested">Butuh Dokumen</option>
                            <option value="pending_approval">Pending Approval</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
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
            <span id="registration-count">{{ $registrations->total() }} pendaftaran ditemukan</span>
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
                        <th class="py-3 fw-semibold">Email</th>
                        <th class="py-3 fw-semibold">No. Kartu Temp</th>
                        <th class="py-3 fw-semibold">Status</th>
                        <th class="py-3 fw-semibold">Tanggal Daftar</th>
                        <th class="py-3 fw-semibold text-center">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="registration-table-body">
                    @include('admin.registrations.partials.rows')
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="border-top border-m365 p-3 bg-m365-gray">
                <div id="registration-pagination">
                    @include('admin.registrations.partials.pagination')
                </div>
            </div>
        </div>
    </div>

    @include('admin.registrations.partials.detail-modal')

@endsection

@push('scripts')
    <script>
        (function(){
            const csrf = "{{ csrf_token() }}";
            const $body = document.getElementById('registration-table-body');
            const $pagination = document.getElementById('registration-pagination');
            const $search = document.getElementById('search-registration');
            const $btnReview = document.getElementById('btn-review');
            const $btnApprove = document.getElementById('btn-approve');
            const $btnReject = document.getElementById('btn-reject');
            const $btnRefresh = document.getElementById('btn-refresh');
            const $btnFilter = document.getElementById('btn-filter');
            const $filterDropdown = document.getElementById('filter-dropdown');
            const $filterStatus = document.getElementById('filter-status');
            const $btnApplyFilter = document.getElementById('btn-apply-filter');
            const $btnClearFilter = document.getElementById('btn-clear-filter');
            const selectAll = document.getElementById('select-all');
            const $count = document.getElementById('registration-count');

            let currentFilters = { status: '' };

            // Toggle filter dropdown
            $btnFilter.addEventListener('click', () => {
                $filterDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!$btnFilter.contains(e.target) && !$filterDropdown.contains(e.target)) {
                    $filterDropdown.classList.remove('show');
                }
            });

            // Apply filters
            $btnApplyFilter.addEventListener('click', () => {
                currentFilters.status = $filterStatus.value;
                $filterDropdown.classList.remove('show');
                fetchRegistrations();
            });

            // Clear filters
            $btnClearFilter.addEventListener('click', () => {
                $filterStatus.value = '';
                currentFilters = { status: '' };
                $filterDropdown.classList.remove('show');
                fetchRegistrations();
            });

            function qs(url, params) {
                const u = new URL(url, location.origin);
                if (params) Object.keys(params).forEach(k => {
                    if (params[k] !== '') u.searchParams.set(k, params[k]);
                });
                return u.toString();
            }

            function renderResponse(data) {
                if (data && typeof data === 'object' && data.rows) {
                    $body.innerHTML = data.rows;
                    $pagination.innerHTML = data.pagination || '';
                    if (data.total !== undefined) {
                        $count.textContent = `${data.total} pendaftaran ditemukan`;
                    }
                    attachRowHandlers();
                    toggleButtons();
                } else {
                    $body.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load</td></tr>';
                }
            }

            async function fetchRegistrations(url = "{{ route('admin.registrations.index') }}", q = null) {
                try {
                    const params = {};
                    if (q === null) q = $search.value.trim();
                    if (q !== '') params.q = q;
                    if (currentFilters.status) params.status = currentFilters.status;

                    const u = qs(url, params);

                    const res = await fetch(u, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    renderResponse(data);
                } catch (err) {
                    console.error(err);
                    $body.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Error loading</td></tr>';
                }
            }

            function debounce(fn, delay=220){
                let t;
                return (...args)=>{
                    clearTimeout(t);
                    t = setTimeout(()=>fn(...args), delay);
                };
            }

            $search.addEventListener('keyup', debounce(()=> fetchRegistrations(), 220));

            document.addEventListener('click', function(e){
                const a = e.target.closest('#registration-pagination a');
                if (a) {
                    e.preventDefault();
                    fetchRegistrations(a.href);
                }
            });

            function attachRowHandlers(){
                document.querySelectorAll('.select-registration').forEach(ch => {
                    ch.onchange = function(){
                        const row = this.closest('tr');
                        if (this.checked) {
                            row.classList.add('bg-m365-selected');
                        } else {
                            row.classList.remove('bg-m365-selected');
                            selectAll.checked = false;
                        }
                        toggleButtons();
                    };
                });

                // View detail buttons
                document.querySelectorAll('.btn-view-detail').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const id = this.getAttribute('data-id');
                        await showDetail(id);
                    });
                });
            }

            function getSelectedIds(){
                return Array.from(document.querySelectorAll('.select-registration:checked')).map(i => i.value);
            }

            function toggleButtons(){
                const count = getSelectedIds().length;
                $btnReview.disabled = (count !== 1);
                $btnApprove.disabled = (count === 0);
                $btnReject.disabled = (count === 0);
            }

            selectAll.addEventListener('change', function(){
                document.querySelectorAll('.select-registration').forEach(c=> {
                    c.checked = this.checked;
                    const row = c.closest('tr');
                    if (this.checked) {
                        row.classList.add('bg-m365-selected');
                    } else {
                        row.classList.remove('bg-m365-selected');
                    }
                });
                toggleButtons();
            });

            $btnRefresh.addEventListener('click', ()=> {
                $search.value = '';
                $filterStatus.value = '';
                currentFilters = { status: '' };
                fetchRegistrations();
            });

            // Review single
            $btnReview.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (ids.length !== 1) return alert('Pilih tepat satu pendaftaran');
                const id = ids[0];
                await showDetail(id);
            });

            // Show detail modal
            async function showDetail(id) {
                try {
                    const res = await fetch(`{{ url('admin/registrations') }}/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to fetch');

                    const reg = await res.json();

                    // Fill modal
                    document.getElementById('detail-id').value = reg.id;
                    document.getElementById('detail-name').textContent = reg.name;
                    document.getElementById('detail-email').textContent = reg.email;
                    document.getElementById('detail-phone').textContent = reg.phone || '-';
                    document.getElementById('detail-address').textContent = reg.address;
                    document.getElementById('detail-status').textContent = reg.status;
                    document.getElementById('detail-temp-card').textContent = reg.temp_card_number || '-';

                    // Documents
                    if (reg.id_document) {
                        document.getElementById('detail-id-doc').href = `/storage/${reg.id_document}`;
                        document.getElementById('detail-id-doc').style.display = 'inline';
                    }
                    if (reg.address_proof) {
                        document.getElementById('detail-address-doc').href = `/storage/${reg.address_proof}`;
                        document.getElementById('detail-address-doc').style.display = 'inline';
                    }

                    new bootstrap.Modal(document.getElementById('modalDetailRegistration')).show();
                } catch (err) {
                    alert('Failed to load details: ' + err.message);
                }
            }

            // Bulk Approve
            $btnApprove.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (!ids.length) return alert('Pilih pendaftaran terlebih dahulu');
                if (!confirm(`Approve ${ids.length} pendaftaran?`)) return;

                try {
                    const res = await fetch("{{ route('admin.registrations.bulkApprove') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids })
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Approval failed');

                    fetchRegistrations();
                    alert(data.message);
                } catch (err) {
                    alert(err.message);
                }
            });

            // Bulk Reject
            $btnReject.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (!ids.length) return alert('Pilih pendaftaran terlebih dahulu');

                const reason = prompt('Alasan penolakan:');
                if (!reason) return;

                if (!confirm(`Reject ${ids.length} pendaftaran?`)) return;

                try {
                    const res = await fetch("{{ route('admin.registrations.bulkReject') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids, reason })
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Rejection failed');

                    fetchRegistrations();
                    alert(data.message);
                } catch (err) {
                    alert(err.message);
                }
            });

            attachRowHandlers();
            toggleButtons();
        })();
    </script>
@endpush
