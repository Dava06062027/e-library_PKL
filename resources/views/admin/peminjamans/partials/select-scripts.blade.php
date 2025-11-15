<script>
    $(document).ready(function() {
        // ===== GLOBAL VARS =====
        let selectedBooksData = {};
        let currentBukuId = null;
        let bukuTable = null;
        let memberTable = null;
        let selectedMember = null;

        // ===== HELPER FUNCTIONS =====
        function updateSelectedBooksDisplay() {
            const container = $('#selected-books-container');
            const list = $('#selected-books-list');
            const btnAddBuku = $('#btn-add-buku');

            const bukuIds = Object.keys(selectedBooksData);

            if (bukuIds.length === 0) {
                container.hide();
                btnAddBuku.prop('disabled', false);
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

            // Update member badge and disable button if max reached
            if (selectedMember) {
                const currentActive = selectedMember.pinjaman_aktif;
                const maxAllowed = 2 - currentActive; // Max buku yang bisa dipilih di transaksi ini

                // Show badge: "X/2 Aktif, Y Slot Tersisa"
                // Y = maxAllowed - totalEksemplar (sisa slot untuk transaksi ini)
                const remainingForTransaction = Math.max(0, maxAllowed - totalEksemplar);

                $('#selected-member-pinjaman').text(
                    `${currentActive}/2 Aktif, ${remainingForTransaction} Slot Tersisa`
                );

                // Disable add book button if already selected max for this transaction
                // Example: Member punya 0 aktif, max bisa pilih 2 buku
                // Setelah pilih 2 buku, button disabled
                if (totalEksemplar >= maxAllowed) {
                    btnAddBuku.prop('disabled', true);
                    btnAddBuku.attr('title', 'Sudah mencapai limit untuk transaksi ini');
                } else {
                    btnAddBuku.prop('disabled', false);
                    btnAddBuku.attr('title', '');
                }
            }
        }

        $(document).on('click', '.btn-remove-buku', function() {
            const bukuId = $(this).data('buku-id');
            delete selectedBooksData[bukuId];
            updateSelectedBooksDisplay();
        });

        // ===== MEMBER MODAL =====
        $('#modalSelectMember').on('shown.bs.modal', function () {
            if (!memberTable) {
                memberTable = $('#member-table').DataTable({
                    ajax: {
                        url: '{{ route("admin.peminjamans.searchMemberDatatable") }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            console.error('Member DataTables Ajax Error:', xhr.responseText);
                            alert('Error loading members: ' + xhr.status);
                        }
                    },
                    serverSide: true,
                    processing: true,
                    searching: true,
                    columns: [
                        { data: 'id' },
                        { data: 'name' },
                        { data: 'email' },
                        {
                            data: 'pinjaman_aktif',
                            render: function(data) {
                                return `<span class="badge bg-secondary">${data}/2 Aktif</span>`;
                            }
                        },
                        {
                            data: 'status',
                            render: function(data, type, row) {
                                return `<span class="badge bg-${row.status_color}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function (data) {
                                if (data.can_borrow) {
                                    return `<button class="btn btn-primary btn-sm btn-pilih-member" data-id="${data.id}" data-name="${data.name}" data-email="${data.email}" data-pinjaman="${data.pinjaman_aktif}">Pilih</button>`;
                                } else {
                                    return `<button class="btn btn-secondary btn-sm" disabled>Full</button>`;
                                }
                            }
                        }
                    ],
                    paging: true,
                    pageLength: 10,
                });
            }
        });

        // Member search
        $('#search-member-modal').on('input', debounce(function () {
            if (memberTable) {
                memberTable.search(this.value).draw();
            }
        }, 300));

        // ===== PILIH MEMBER =====
        $(document).on('click', '.btn-pilih-member', async function () {
            const memberId = $(this).data('id');
            const memberName = $(this).data('name');
            const memberEmail = $(this).data('email');
            const pinjamanAktif = $(this).data('pinjaman');

            try {
                const url = '{{ route("admin.peminjamans.checkMemberEligibility", ":id") }}'.replace(':id', memberId);
                const res = await fetch(url);
                const data = await res.json();

                if (!data.eligible) {
                    alert(data.message);
                    return;
                }

                selectedMember = {
                    id: memberId,
                    name: memberName,
                    email: memberEmail,
                    pinjaman_aktif: pinjamanAktif,
                    remaining_slots: data.remaining_slots
                };

                $('#selected-member-id').val(memberId);
                $('#selected-member-name').text(memberName);
                $('#selected-member-email').text(memberEmail);
                $('#selected-member-pinjaman').text(`${pinjamanAktif}/2 Aktif, ${data.remaining_slots} Slot Tersisa`);

                $('#btn-select-member').hide();
                $('#selected-member-display').show();

                // Update button state based on selected books
                updateSelectedBooksDisplay();

                $('#modalSelectMember').modal('hide');
                $('#modalNewPeminjaman').modal('show');

            } catch (err) {
                console.error('Check eligibility error:', err);
                alert('Error checking member eligibility: ' + err.message);
            }
        });

        // Remove member
        $('#btn-remove-member').on('click', function() {
            selectedMember = null;
            selectedBooksData = {}; // Clear selected books too
            $('#selected-member-id').val('');
            $('#selected-member-display').hide();
            $('#btn-select-member').show();
            $('#selected-books-container').hide();
            $('#btn-add-buku').prop('disabled', false);
        });

        // ===== BUKU MODAL =====
        $('#modalSelectBuku').on('shown.bs.modal', function () {
            $('#section-pilih-judul').show();
            $('#section-pilih-eksemplar').hide();

            if (!bukuTable) {
                bukuTable = $('#buku-table').DataTable({
                    ajax: {
                        url: '{{ route("admin.peminjamans.searchBukuDatatable") }}',
                        type: 'GET',
                        error: function(xhr, error, thrown) {
                            console.error('Buku DataTables Ajax Error:', xhr.responseText);
                            alert('Error loading books: ' + xhr.status);
                        }
                    },
                    serverSide: true,
                    processing: true,
                    searching: true,
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
                    paging: true,
                    pageLength: 10,
                });
            }
        });

        // Buku search
        $('#search-buku-modal').on('input', debounce(function () {
            if (bukuTable) {
                bukuTable.search(this.value).draw();
            }
        }, 300));

        // Filter dropdown toggle
        $('#btn-filter-buku').on('click', function (e) {
            e.stopPropagation();
            $('#filter-dropdown-buku').toggleClass('show');
        });

        // Close dropdown on click outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#btn-filter-buku, #filter-dropdown-buku').length) {
                $('#filter-dropdown-buku').removeClass('show');
            }
        });

        // Apply filter tahun
        $('#btn-apply-filter-buku').on('click', function () {
            const tahun = $('#filter-tahun').val().trim();

            if (bukuTable) {
                if (tahun) {
                    bukuTable.column(3).search(tahun, false, false).draw();
                } else {
                    bukuTable.column(3).search('', false, false).draw();
                }
            }
            $('#filter-dropdown-buku').removeClass('show');
        });

        // ===== PILIH BUKU =====
        $(document).on('click', '.btn-pilih-buku', async function () {
            const bukuId = $(this).data('id');
            const judul = $(this).data('judul');

            currentBukuId = bukuId;
            $('#selected-judul-text').text(judul);

            $('#section-pilih-judul').hide();
            $('#section-pilih-eksemplar').show();

            try {
                const url = '{{ route("admin.peminjamans.availableEksemplarByBuku", ":id") }}'.replace(':id', bukuId);
                const res = await fetch(url);

                if (!res.ok) throw new Error('Failed to load eksemplar');

                const eksemplarList = await res.json();
                const tbody = $('#eksemplar-table-body');
                tbody.empty();

                if (eksemplarList.length === 0) {
                    tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada eksemplar tersedia (kondisi Baik & status Tersedia)</td></tr>');
                    return;
                }

                eksemplarList.forEach(eks => {
                    tbody.append(`
                    <tr>
                        <td><input type="checkbox" class="form-check-input select-eksemplar" value="${eks.id}" data-barcode="${eks.barcode}" data-kondisi="${eks.kondisi}" data-status="${eks.status}" data-sumber="${eks.sumber}"></td>
                        <td>${eks.barcode}</td>
                        <td><span class="badge bg-success">${eks.kondisi}</span></td>
                        <td><span class="badge bg-info">${eks.status}</span></td>
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

        // Back to judul
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

        $(document).on('change', '.select-eksemplar', function() {
            updateSelectedEksemplarCount();
        });

        function updateSelectedEksemplarCount() {
            const count = $('.select-eksemplar:checked').length;
            $('#selected-eksemplar-count').text(count);
        }

        // Apply range
        $('#btn-apply-range').on('click', function() {
            const rangeInput = $('#range-barcode').val().trim();
            if (!rangeInput) return alert('Masukkan range barcode!');

            const parts = rangeInput.split('-');
            if (parts.length !== 2) return alert('Format salah! Gunakan: BARCODE_AWAL-BARCODE_AKHIR');

            const start = parts[0].trim();
            const end = parts[1].trim();

            $('.select-eksemplar').prop('checked', false);

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

        // ===== KONFIRMASI EKSEMPLAR =====
        $('#btn-confirm-eksemplar').on('click', function() {
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

            // Check if exceeds member limit
            if (selectedMember) {
                const totalEksemplarNow = Object.values(selectedBooksData).reduce((sum, data) => sum + data.eksemplar.length, 0);
                const totalAfterAdd = totalEksemplarNow + selectedEks.length;
                const maxAllowed = 2 - selectedMember.pinjaman_aktif;

                if (totalAfterAdd > maxAllowed) {
                    alert(`Tidak bisa menambah! Member hanya bisa meminjam maksimal ${maxAllowed} eksemplar dalam transaksi ini.`);
                    return;
                }
            }

            const judul = $('#selected-judul-text').text();
            selectedBooksData[currentBukuId] = {
                judul: judul,
                eksemplar: selectedEks,
                eksemplarDetails: selectedEksDetails
            };

            updateSelectedBooksDisplay();

            $('#modalSelectBuku').modal('hide');
            $('#modalNewPeminjaman').modal('show');

            $('#section-pilih-eksemplar').hide();
            $('#section-pilih-judul').show();
            currentBukuId = null;
        });

        // ===== SUBMIT NEW PEMINJAMAN =====
        $('#form-new-peminjaman').on('submit', async function(e) {
            e.preventDefault();

            if (!selectedMember) {
                alert('Pilih member terlebih dahulu!');
                return;
            }

            const allEksemplarIds = [];
            Object.values(selectedBooksData).forEach(data => {
                allEksemplarIds.push(...data.eksemplar);
            });

            if (allEksemplarIds.length === 0) {
                return alert('Pilih minimal 1 eksemplar dari buku!');
            }

            const maxAllowed = 2 - selectedMember.pinjaman_aktif;

            if (allEksemplarIds.length > maxAllowed) {
                alert(`Member hanya bisa meminjam maksimal ${maxAllowed} eksemplar dalam transaksi ini (sudah ada ${selectedMember.pinjaman_aktif} pinjaman aktif)`);
                return;
            }

            try {
                const checkUrl = '{{ route("admin.peminjamans.checkMemberEligibility", ":id") }}'.replace(':id', selectedMember.id);
                const checkRes = await fetch(checkUrl);
                const checkData = await checkRes.json();

                if (!checkData.eligible) {
                    alert('Member tidak eligible: ' + checkData.message);
                    return;
                }

                const formData = {
                    id_member: selectedMember.id,
                    id_buku_items: allEksemplarIds,
                    tanggal_pinjam: $('[name="tanggal_pinjam"]').val(),
                    tanggal_kembali_rencana: $('[name="tanggal_kembali_rencana"]').val(),
                    catatan: $('textarea[name="catatan"]').val()
                };

                const res = await fetch('{{ route("admin.peminjamans.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.error || data.message || 'Failed');

                $('#modalNewPeminjaman').modal('hide');
                alert(data.message || 'Peminjaman berhasil dibuat!');

                // Reset
                selectedMember = null;
                selectedBooksData = {};
                $('#form-new-peminjaman')[0].reset();
                $('#selected-member-display').hide();
                $('#btn-select-member').show();
                $('#selected-books-container').hide();
                $('#btn-add-buku').prop('disabled', false);

                // Auto refresh using global function
                if (typeof window.fetchPeminjamans === 'function') {
                    console.log('Calling fetchPeminjamans after create'); // Debug
                    await window.fetchPeminjamans();
                } else {
                    console.log('fetchPeminjamans not found, reloading page'); // Debug
                    location.reload();
                }

            } catch (err) {
                console.error('Submit error:', err);
                alert(err.message || 'Error membuat peminjaman');
            }
        });

        // Debounce utility
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
    });
</script>
