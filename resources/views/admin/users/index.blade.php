@extends('layouts.app')

@section('content')
    <style>
        /* Minimal custom styles untuk warna yang spesifik Microsoft 365 */
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
            <button id="btn-new-user" class="btn btn-m365 d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>New user</span>
            </button>
            <div class="vr"></div>
            <button id="btn-detail-user" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-person-badge"></i>
                <span>Detail</span>
            </button>
            <button id="btn-edit-user" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
                <i class="bi bi-pencil"></i>
                <span>Edit</span>
            </button>
            <button id="btn-delete-user" class="btn btn-m365 d-flex align-items-center gap-2" disabled>
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
                <input id="search-user" type="text" class="form-control search-input" placeholder="Search">
            </div>
            <div class="position-relative">
                <button id="btn-filter" class="btn btn-m365 d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Add filter</span>
                </button>
                <div id="filter-dropdown" class="filter-dropdown">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Role</label>
                        <select id="filter-role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            <option value="Admin">Admin</option>
                            <option value="Officer">Officer</option>
                            <option value="Member">Member</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter by Status</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
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
            <span id="user-count">{{ $users->total() }} users found</span>
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
                        <th class="py-3 fw-semibold">Name</th>
                        <th class="py-3 fw-semibold">Email</th>
                        <th class="py-3 fw-semibold">Role</th>
                        <th class="py-3 fw-semibold">Status</th>
                        <th class="py-3 fw-semibold text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="user-table-body">
                    @include('admin.users.partials.rows')
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="border-top border-m365 p-3 bg-m365-gray">
                <div id="user-pagination">
                    @include('admin.users.partials.pagination')
                </div>
            </div>
        </div>
    </div>

    @include('admin.users.partials.new-user-modal')
    @include('admin.users.partials.edit-user-modal')
    @include('admin.users.partials.member-detail-modal')

@endsection

@push('scripts')
    <script>
        (function(){
            const csrf = "{{ csrf_token() }}";
            const userRole = "{{ auth()->user()->role }}";
            const $body = document.getElementById('user-table-body');
            const $pagination = document.getElementById('user-pagination');
            const $search = document.getElementById('search-user');
            const $btnDetail = document.getElementById('btn-detail-user');
            const $btnEdit = document.getElementById('btn-edit-user');
            const $btnDelete = document.getElementById('btn-delete-user');
            const $btnNew = document.getElementById('btn-new-user');
            const $btnRefresh = document.getElementById('btn-refresh');
            const $btnFilter = document.getElementById('btn-filter');
            const $filterDropdown = document.getElementById('filter-dropdown');
            const $filterRole = document.getElementById('filter-role');
            const $filterStatus = document.getElementById('filter-status');
            const $btnApplyFilter = document.getElementById('btn-apply-filter');
            const $btnClearFilter = document.getElementById('btn-clear-filter');
            const selectAll = document.getElementById('select-all');
            const $userCount = document.getElementById('user-count');

            let currentFilters = { role: '', status: '' };
            let onlineStatusInterval;

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
                currentFilters.role = $filterRole.value;
                currentFilters.status = $filterStatus.value;
                $filterDropdown.classList.remove('show');
                fetchUsers();
            });

            // Clear filters
            $btnClearFilter.addEventListener('click', () => {
                $filterRole.value = '';
                $filterStatus.value = '';
                currentFilters = { role: '', status: '' };
                $filterDropdown.classList.remove('show');
                fetchUsers();
            });

            // Update online status
            async function updateOnlineStatus() {
                try {
                    const res = await fetch("{{ route('admin.users.onlineStatus') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    document.querySelectorAll('tr[data-id]').forEach(row => {
                        const userId = row.getAttribute('data-id');
                        const statusCell = row.querySelector('td:nth-child(5)');
                        if (statusCell && data[userId] !== undefined) {
                            const isOnline = data[userId];
                            const statusDot = statusCell.querySelector('.status-online, .status-offline');
                            const statusText = statusCell.querySelector('.small');

                            if (statusDot) {
                                statusDot.className = isOnline ? 'status-online' : 'status-offline';
                            }
                            if (statusText) {
                                statusText.textContent = isOnline ? 'Online' : 'Offline';
                            }
                        }
                    });
                } catch (err) {
                    console.error('Failed to update online status:', err);
                }
            }

            // Start online status polling
            function startOnlineStatusPolling() {
                if (onlineStatusInterval) {
                    clearInterval(onlineStatusInterval);
                }
                updateOnlineStatus(); // Initial call
                onlineStatusInterval = setInterval(updateOnlineStatus, 10000); // Every 10 seconds
            }

            // Stop online status polling
            function stopOnlineStatusPolling() {
                if (onlineStatusInterval) {
                    clearInterval(onlineStatusInterval);
                    onlineStatusInterval = null;
                }
            }

            // Start polling when page loads
            startOnlineStatusPolling();

            // Stop polling when leaving page
            window.addEventListener('beforeunload', stopOnlineStatusPolling);

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
                        $userCount.textContent = `${data.total} users found`;
                    }
                    attachRowHandlers();
                    attachDetailHandlers();
                    toggleButtons();
                    setTimeout(updateOnlineStatus, 500);
                } else {
                    $body.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load</td></tr>';
                }
            }

            async function fetchUsers(url = "{{ route('admin.users') }}", q = null) {
                try {
                    const params = {};
                    if (q === null) q = $search.value.trim();
                    if (q !== '') params.q = q;
                    if (currentFilters.role) params.role = currentFilters.role;
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
                    $body.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading</td></tr>';
                }
            }

            function debounce(fn, delay=220){
                let t;
                return (...args)=>{
                    clearTimeout(t);
                    t = setTimeout(()=>fn(...args), delay);
                };
            }

            $search.addEventListener('keyup', debounce(()=> fetchUsers(), 220));

            document.addEventListener('click', function(e){
                const a = e.target.closest('#user-pagination a');
                if (a) {
                    e.preventDefault();
                    fetchUsers(a.href);
                }
            });

            function attachRowHandlers(){
                document.querySelectorAll('.select-user').forEach(ch => {
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
            }

            function attachDetailHandlers() {
                document.querySelectorAll('.btn-view-detail').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const id = this.getAttribute('data-id');
                        await showMemberDetail(id);
                    });
                });
            }

            function getSelectedIds(){
                return Array.from(document.querySelectorAll('.select-user:checked')).map(i => i.value);
            }

            function toggleButtons(){
                const count = getSelectedIds().length;
                $btnDelete.disabled = (count === 0);
                $btnEdit.disabled = (count !== 1);
                $btnDetail.disabled = (count !== 1);
            }

            selectAll.addEventListener('change', function(){
                document.querySelectorAll('.select-user').forEach(c=> {
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
                $filterRole.value = '';
                $filterStatus.value = '';
                currentFilters = { role: '', status: '' };
                fetchUsers();
            });

            // NEW USER - dengan form lengkap
            $btnNew.addEventListener('click', ()=> {
                const modal = new bootstrap.Modal(document.getElementById('modalNewUser'));
                document.getElementById('form-new-user').reset();
                modal.show();
            });

            document.getElementById('form-new-user').addEventListener('submit', async function(e){
                e.preventDefault();
                const formData = new FormData(this);

                // Add approved_by and approved_at for direct creation
                formData.append('approved_by', '{{ auth()->id() }}');
                formData.append('approved_at', new Date().toISOString());

                try {
                    const res = await fetch("{{ route('admin.users.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : data.error || data.message;
                        throw new Error(errors);
                    }
                    bootstrap.Modal.getInstance(document.getElementById('modalNewUser')).hide();
                    fetchUsers();
                    alert(data.message || 'Member created successfully');
                } catch (err) {
                    alert(err.message || 'Error creating user');
                }
            });

            // EDIT USER
            $btnEdit.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (ids.length !== 1) return alert('Select exactly one user');
                try {
                    const res = await fetch("{{ url('admin/users') }}/" + ids[0], {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to fetch user');

                    const user = await res.json();

                    // Fill form
                    document.getElementById('edit-user-id').value = user.id;
                    document.getElementById('edit-name').value = user.name;
                    document.getElementById('edit-email').value = user.email;
                    document.getElementById('edit-phone').value = user.phone || '';
                    document.getElementById('edit-birth-date').value = user.birth_date || '';
                    document.getElementById('edit-address').value = user.address || '';
                    document.getElementById('edit-nik').value = user.nik || '';
                    document.getElementById('edit-role').value = user.role;
                    document.getElementById('edit-password').value = '';
                    document.getElementById('edit-password_confirmation').value = '';

                    // Show current photos if exists
                    if (user.ktp_photo) {
                        document.getElementById('current-ktp-preview').style.display = 'block';
                        document.getElementById('current-ktp-img').src = '/storage/' + user.ktp_photo;
                    } else {
                        document.getElementById('current-ktp-preview').style.display = 'none';
                    }

                    if (user.photo) {
                        document.getElementById('current-photo-preview').style.display = 'block';
                        document.getElementById('current-photo-img').src = '/storage/' + user.photo;
                    } else {
                        document.getElementById('current-photo-preview').style.display = 'none';
                    }

                    const roleField = document.getElementById('edit-role');
                    const roleContainer = roleField.closest('.mb-3');

                    if (userRole === 'Officer') {
                        roleContainer.style.display = 'none';
                    } else {
                        roleContainer.style.display = 'block';
                    }

                    new bootstrap.Modal(document.getElementById('modalEditUser')).show();
                } catch (err) {
                    alert('Failed to fetch user: ' + err.message);
                }
            });

            document.getElementById('form-edit-user').addEventListener('submit', async function(e){
                e.preventDefault();
                const id = document.getElementById('edit-user-id').value;
                const formData = new FormData(this);

                try {
                    const res = await fetch("{{ url('admin/users') }}/" + id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    // Add _method for PUT
                    formData.append('_method', 'PUT');

                    const responseData = await res.json();

                    if (!res.ok) {
                        const errors = responseData.errors ? Object.values(responseData.errors).flat().join('\n') : responseData.error || responseData.message;
                        throw new Error(errors);
                    }

                    bootstrap.Modal.getInstance(document.getElementById('modalEditUser')).hide();
                    fetchUsers();
                    alert(responseData.message || 'Updated successfully');
                } catch (err) {
                    alert(err.message || 'Update failed');
                }
            });

            // DETAIL MEMBER
            $btnDetail.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (ids.length !== 1) return alert('Select exactly one user');
                await showMemberDetail(ids[0]);
            });

            async function showMemberDetail(id) {
                try {
                    const res = await fetch("{{ url('admin/users') }}/" + id, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to fetch user');

                    const user = await res.json();

                    // Fill detail modal - LENGKAP!
                    document.getElementById('detail-name').textContent = user.name || '-';
                    document.getElementById('detail-email').textContent = user.email || '-';
                    document.getElementById('detail-nik').textContent = user.nik || '-';
                    document.getElementById('detail-phone').textContent = user.phone || '-';
                    document.getElementById('detail-birth-date').textContent = user.birth_date ? new Date(user.birth_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';
                    document.getElementById('detail-address').textContent = user.address || '-';

                    // Role badge
                    const roleBadge = document.getElementById('detail-role');
                    roleBadge.textContent = user.role;
                    roleBadge.className = 'badge ' + (user.role === 'Admin' ? 'bg-danger' : user.role === 'Officer' ? 'bg-warning text-dark' : 'bg-primary');

                    // Approval info
                    if (user.approver) {
                        document.getElementById('detail-approved-by').textContent = user.approver.name;
                    } else {
                        document.getElementById('detail-approved-by').textContent = '-';
                    }

                    if (user.approved_at) {
                        const approvedDate = new Date(user.approved_at);
                        document.getElementById('detail-approved-at').textContent = approvedDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        document.getElementById('detail-approved-at').textContent = '-';
                    }

                    // Status
                    const statusEl = document.getElementById('detail-status');
                    if (user.email_verified_at) {
                        statusEl.innerHTML = '<span class="badge bg-success">Verified</span>';
                    } else {
                        statusEl.innerHTML = '<span class="badge bg-warning">Unverified</span>';
                    }

                    // Profile Photo
                    const photoImg = document.getElementById('detail-photo');
                    if (user.photo) {
                        photoImg.src = '/storage/' + user.photo;
                        photoImg.onerror = function() {
                            this.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=150&background=667eea&color=fff';
                        };
                    } else {
                        photoImg.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=150&background=667eea&color=fff';
                    }

                    // KTP Photo
                    const ktpImg = document.getElementById('detail-ktp');
                    const ktpContainer = document.getElementById('detail-ktp-container');
                    if (user.ktp_photo) {
                        ktpImg.src = '/storage/' + user.ktp_photo;
                        ktpImg.onerror = function() {
                            ktpContainer.innerHTML = '<div class="alert alert-warning"><small>Foto KTP tidak dapat dimuat</small></div>';
                        };
                        ktpContainer.style.display = 'block';
                    } else {
                        ktpContainer.innerHTML = '<div class="alert alert-secondary"><small><i class="bi bi-exclamation-circle me-1"></i>Belum ada foto KTP</small></div>';
                    }

                    // Fill member card
                    document.getElementById('card-member-name').textContent = user.name ? user.name.toUpperCase() : 'NO NAME';

                    // Member ID (gunakan NIK atau ID user)
                    const memberId = user.nik || String(user.id).padStart(11, '0');
                    document.getElementById('card-member-id').textContent = memberId;
                    document.getElementById('card-nik-text').textContent = '* ' + memberId.match(/.{1,1}/g).join(' ') + ' *';

                    // Card Photo
                    const cardPhoto = document.getElementById('card-photo');
                    if (user.photo) {
                        cardPhoto.src = '/storage/' + user.photo;
                        cardPhoto.onerror = function() {
                            this.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=200&background=667eea&color=fff';
                        };
                    } else {
                        cardPhoto.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=200&background=667eea&color=fff';
                    }

                    // Generate barcode
                    const barcodeEl = document.getElementById('card-barcode');
                    if (barcodeEl && typeof JsBarcode !== 'undefined') {
                        try {
                            JsBarcode(barcodeEl, memberId, {
                                format: "CODE128",
                                width: 2,
                                height: 50,
                                displayValue: false,
                                margin: 0
                            });
                        } catch (err) {
                            console.error('Barcode generation failed:', err);
                            // Fallback: create simple bars
                            barcodeEl.innerHTML = '<rect width="500" height="50" fill="#000000"/>';
                        }
                    }

                    new bootstrap.Modal(document.getElementById('modalMemberDetail')).show();
                } catch (err) {
                    console.error('Error:', err);
                    alert('Failed to fetch member detail: ' + err.message);
                }
            }

            // DELETE
            $btnDelete.addEventListener('click', async function(){
                const ids = getSelectedIds();
                if (!ids.length) return alert('Select users first');
                if (!confirm(`Delete ${ids.length} users?`)) return;
                try {
                    const res = await fetch("{{ route('admin.users.destroySelected') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ ids })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.error || data.message || 'Delete failed');
                    }
                    fetchUsers();
                    alert(data.message || 'Deleted');
                } catch (err) {
                    alert(err.message || 'Delete failed');
                }
            });

            attachRowHandlers();
            attachDetailHandlers();
            toggleButtons();
        })();
    </script>
@endpush
