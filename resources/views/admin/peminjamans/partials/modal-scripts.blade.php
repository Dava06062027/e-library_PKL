<script>
    // =====================================================
    // RETURN MODAL HANDLER
    // =====================================================
    document.getElementById('btn-return-peminjaman').addEventListener('click', async function() {
        const selected = Array.from(document.querySelectorAll('.select-peminjaman:checked'));

        if (selected.length === 0) {
            alert('Pilih setidaknya satu transaksi!');
            return;
        }

        // For simplicity, process one at a time (can be enhanced for batch)
        const peminjamanId = selected[0].value;

        try {
            const res = await fetch(`{{ url('admin/peminjamans') }}/${peminjamanId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('Failed to fetch');

            const peminjaman = await res.json();

            // Fill modal info
            document.getElementById('return-id-peminjaman').value = peminjamanId;
            document.getElementById('return-transaction-number').textContent = peminjaman.transaction_number;
            document.getElementById('return-member-name').textContent = peminjaman.member_name;
            document.getElementById('return-due-date').textContent = peminjaman.tanggal_kembali_rencana;
            document.getElementById('return-days-late').textContent = peminjaman.days_late > 0 ? `${peminjaman.days_late} hari` : 'Tepat Waktu';
            document.getElementById('return-fine-days').textContent = `${peminjaman.days_late} hari`;

            const dendaPerHari = 1000;
            const totalLateFee = peminjaman.days_late * dendaPerHari;
            document.getElementById('return-fine-late').textContent = `Rp ${totalLateFee.toLocaleString('id-ID')}`;

            // Populate items (only Dipinjam items)
            const itemsContainer = document.getElementById('return-items-container');
            const dipinjamItems = peminjaman.items.filter(item => item.status_item === 'Dipinjam');

            if (dipinjamItems.length === 0) {
                itemsContainer.innerHTML = '<p class="text-muted">Tidak ada item yang perlu dikembalikan.</p>';
                return;
            }

            itemsContainer.innerHTML = dipinjamItems.map((item, idx) => `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="form-check mb-2">
                        <input class="form-check-input return-item-check" type="checkbox" value="${item.id}" id="return-item-${item.id}" checked>
                        <label class="form-check-label" for="return-item-${item.id}">
                            <strong>${item.buku_judul}</strong> (${item.barcode})
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small">Kondisi Kembali</label>
                            <select class="form-select form-select-sm return-kondisi" data-item-id="${item.id}" required>
                                <option value="Baik">Baik</option>
                                <option value="Cukup">Cukup</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Hilang">Hilang</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Denda Kerusakan</label>
                            <input type="number" class="form-control form-control-sm return-denda-rusak" data-item-id="${item.id}" value="0" min="0">
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

            // Calculate total fine on change
            function updateTotalFine() {
                let totalDamage = 0;
                document.querySelectorAll('.return-item-check:checked').forEach(cb => {
                    const itemId = cb.value;
                    const damageInput = document.querySelector(`.return-denda-rusak[data-item-id="${itemId}"]`);
                    totalDamage += parseFloat(damageInput.value || 0);
                });

                const totalFine = totalLateFee + totalDamage;
                document.getElementById('return-fine-damage').textContent = `Rp ${totalDamage.toLocaleString('id-ID')}`;
                document.getElementById('return-fine-total').textContent = `Rp ${totalFine.toLocaleString('id-ID')}`;
            }

            // Attach listeners
            document.querySelectorAll('.return-denda-rusak, .return-item-check').forEach(el => {
                el.addEventListener('change', updateTotalFine);
                el.addEventListener('input', updateTotalFine);
            });

            // Auto-set denda based on kondisi
            document.querySelectorAll('.return-kondisi').forEach(select => {
                select.addEventListener('change', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const dendaInput = document.querySelector(`.return-denda-rusak[data-item-id="${itemId}"]`);

                    switch(this.value) {
                        case 'Rusak':
                            dendaInput.value = 50000;
                            break;
                        case 'Hilang':
                            dendaInput.value = 100000;
                            break;
                        default:
                            dendaInput.value = 0;
                    }
                    updateTotalFine();
                });
            });

            updateTotalFine();

            // Show modal
            new bootstrap.Modal(document.getElementById('modalReturnPeminjaman')).show();

        } catch (err) {
            console.error('Error loading return modal:', err);
            alert('Error loading data: ' + err.message);
        }
    });

    // Return form submit
    document.getElementById('form-return-peminjaman').addEventListener('submit', async function(e) {
        e.preventDefault();

        const checkedItems = document.querySelectorAll('.return-item-check:checked');

        if (checkedItems.length === 0) {
            alert('Pilih setidaknya satu item untuk dikembalikan!');
            return;
        }

        const items = Array.from(checkedItems).map(cb => {
            const itemId = cb.value;
            return {
                id_item: itemId,
                kondisi_kembali: document.querySelector(`.return-kondisi[data-item-id="${itemId}"]`).value,
                denda_kerusakan: parseFloat(document.querySelector(`.return-denda-rusak[data-item-id="${itemId}"]`).value) || 0
            };
        });

        const formData = {
            id_peminjaman: document.getElementById('return-id-peminjaman').value,
            items: items,
            tanggal_kembali_aktual: document.getElementById('return-date').value,
            catatan: document.querySelector('#form-return-peminjaman textarea[name="catatan"]').value
        };

        try {
            const res = await fetch('{{ route("admin.peminjamans.return") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await res.json();

            if (!res.ok) {
                // Handle validation errors
                if (data.details) {
                    const errors = Object.values(data.details).flat();
                    throw new Error(errors.join('\n'));
                }
                throw new Error(data.error || 'Failed to process return');
            }

            bootstrap.Modal.getInstance(document.getElementById('modalReturnPeminjaman')).hide();
            alert(`${data.message}\nTotal Denda: Rp ${data.total_denda}\nStatus: ${data.status_transaksi}`);

            // Auto refresh
            if (typeof window.fetchPeminjamans === 'function') {
                await window.fetchPeminjamans();
            } else {
                location.reload();
            }

        } catch (err) {
            console.error('Return error:', err);
            alert('Error: ' + err.message);
        }
    });

    // =====================================================
    // EXTEND MODAL HANDLER
    // =====================================================
    document.getElementById('btn-extend-peminjaman').addEventListener('click', async function() {
        const selected = Array.from(document.querySelectorAll('.select-peminjaman:checked'));

        if (selected.length !== 1) {
            alert('Pilih tepat 1 transaksi untuk diperpanjang!');
            return;
        }

        const peminjamanId = selected[0].value;

        try {
            const res = await fetch(`{{ url('admin/peminjamans') }}/${peminjamanId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('Failed to fetch');

            const peminjaman = await res.json();

            if (!['Dipinjam', 'Diperpanjang'].includes(peminjaman.status_transaksi)) {
                alert('Hanya transaksi Dipinjam/Diperpanjang yang bisa diperpanjang!');
                return;
            }

            // ✅ CHECK: If already extended max times
            const jumlahPerpanjangan = peminjaman.jumlah_perpanjangan || 0;

            if (jumlahPerpanjangan >= 1) {
                // Show warning and disable form
                document.getElementById('extend-warning-max').classList.remove('d-none');
                document.getElementById('extend-form-content').style.display = 'none';
                document.getElementById('extend-submit-btn').disabled = true;
            } else {
                // Show form and enable submit
                document.getElementById('extend-warning-max').classList.add('d-none');
                document.getElementById('extend-form-content').style.display = 'block';
                document.getElementById('extend-submit-btn').disabled = false;
            }

            // Fill modal
            document.getElementById('extend-id-peminjaman').value = peminjamanId;
            document.getElementById('extend-jumlah-perpanjangan').value = jumlahPerpanjangan;
            document.getElementById('extend-transaction-number').textContent = peminjaman.transaction_number;
            document.getElementById('extend-due-date-lama').textContent = peminjaman.tanggal_kembali_rencana;
            document.getElementById('extend-hari-telat').textContent = peminjaman.days_late > 0 ? `${peminjaman.days_late} hari` : '0 hari (Tepat Waktu)';
            document.getElementById('extend-count-display').textContent = `${jumlahPerpanjangan}/1`;

            const biaya = peminjaman.days_late * 1000;
            document.getElementById('extend-denda-telat').textContent = `Rp ${biaya.toLocaleString('id-ID')}`;
            document.getElementById('extend-biaya-display').value = `Rp ${biaya.toLocaleString('id-ID')}`;

            // Set date constraints (rest of the code remains same...)

            // Show modal
            new bootstrap.Modal(document.getElementById('modalExtendPeminjaman')).show();

        } catch (err) {
            console.error('Error loading extend modal:', err);
            alert('Error loading data: ' + err.message);
        }
    });

    // Extend date validation
    document.getElementById('extend-tanggal-baru').addEventListener('change', function() {
        const minDate = new Date(this.min);
        const maxDate = new Date(this.max);
        const selectedDate = new Date(this.value);

        if (selectedDate < minDate) {
            alert('Tanggal perpanjangan minimal besok!');
            this.value = minDate.toISOString().split('T')[0];
        } else if (selectedDate > maxDate) {
            alert('Perpanjangan maksimal 5 hari dari due date lama!');
            this.value = maxDate.toISOString().split('T')[0];
        }
    });

    // Extend form submit
    document.getElementById('form-extend-peminjaman').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            id_peminjaman: document.getElementById('extend-id-peminjaman').value,
            tanggal_kembali_rencana_baru: document.getElementById('extend-tanggal-baru').value,
            catatan: document.querySelector('#form-extend-peminjaman textarea[name="catatan"]').value
        };

        try {
            const res = await fetch('{{ route("admin.peminjamans.extend") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await res.json();

            if (!res.ok) {
                // Handle validation errors
                if (data.details) {
                    const errors = Object.values(data.details).flat();
                    throw new Error(errors.join('\n'));
                }
                throw new Error(data.error || 'Failed to extend');
            }

            bootstrap.Modal.getInstance(document.getElementById('modalExtendPeminjaman')).hide();
            alert(`${data.message}\nDue Date Baru: ${data.new_due_date}\nBiaya: Rp ${data.biaya}\nPerpanjangan ke: ${data.extension_count}`);

            // Auto refresh
            if (typeof window.fetchPeminjamans === 'function') {
                await window.fetchPeminjamans();
            } else {
                location.reload();
            }

        } catch (err) {
            console.error('Extend error:', err);
            alert('Error: ' + err.message);
        }
    });
</script>

{{-- Include the select-scripts from previous implementation --}}
@include('admin.peminjamans.partials.select-scripts')
