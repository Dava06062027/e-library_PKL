<div class="modal fade" id="modalExtendPeminjaman" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-extend-peminjaman" class="modal-content">
            @csrf
            <input type="hidden" name="id_peminjaman" id="extend-id-peminjaman">
            <div class="modal-header">
                <h5 class="modal-title">Perpanjangan Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Info Peminjaman -->
                <div class="alert alert-info" id="extend-info-box">
                    <strong>Info Peminjaman:</strong>
                    <div class="mt-2">
                        <small>Due Date Lama: <strong id="extend-due-date-lama">-</strong></small><br>
                        <small>Hari Telat: <strong id="extend-hari-telat" class="text-danger">-</strong></small><br>
                        <small>Denda Keterlambatan: <strong id="extend-denda-telat" class="text-danger">Rp 0</strong></small>
                    </div>
                </div>

                <!-- Tanggal Kembali Rencana Baru -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Rencana Baru</label>
                    <input type="date" name="tanggal_kembali_rencana_baru" id="extend-tanggal-baru" class="form-control" required>
                    <small class="text-muted">Maksimal 5 hari dari due date lama</small>
                </div>

                <!-- Biaya Perpanjangan (Auto-calculated) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Biaya Perpanjangan</label>
                    <input type="number" name="biaya" id="extend-biaya" class="form-control" value="0" min="0" readonly>
                    <small class="text-muted">Biaya = Denda keterlambatan (jika ada)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>

                <!-- Warning Alert -->
                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Perhatian:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Perpanjangan hanya bisa <strong>maksimal 5 hari</strong> dari due date lama</li>
                        <li>Jika perpanjang setelah hari ke-7, tetap kena denda sesuai hari telat</li>
                        <li>Setelah perpanjang, count denda berhenti dan masuk periode baru</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Submit Perpanjangan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Extend modal date validation & denda calculation
    $('#btn-extend-peminjaman').on('click', async function() {
        const selectedIds = Array.from(document.querySelectorAll('.select-peminjaman:checked')).map(cb => cb.value);

        if (selectedIds.length !== 1) {
            alert('Pilih 1 peminjaman untuk diperpanjang!');
            return;
        }

        const peminjamanId = selectedIds[0];
        $('#extend-id-peminjaman').val(peminjamanId);

        // Fetch peminjaman data
        try {
            const res = await fetch(`{{ url('admin/peminjamans') }}/${peminjamanId}`);
            const peminjaman = await res.json();

            const dueDate = new Date(peminjaman.tanggal_kembali_rencana);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            dueDate.setHours(0, 0, 0, 0);

            // Calculate days late
            const diffTime = today - dueDate;
            const daysLate = Math.max(0, Math.floor(diffTime / (1000 * 60 * 60 * 24)));
            const dendaPerHari = 1000;
            const totalDenda = daysLate * dendaPerHari;

            // Display info
            $('#extend-due-date-lama').text(peminjaman.tanggal_kembali_rencana);
            $('#extend-hari-telat').text(daysLate > 0 ? `${daysLate} hari` : '0 hari (Tepat Waktu)');
            $('#extend-denda-telat').text(`Rp ${totalDenda.toLocaleString('id-ID')}`);
            $('#extend-biaya').val(totalDenda);

            // Set date constraints
            const minDate = new Date();
            minDate.setDate(minDate.getDate() + 1); // Min besok
            const maxDate = new Date(dueDate);
            maxDate.setDate(maxDate.getDate() + 5); // Max due date + 5 hari

            $('#extend-tanggal-baru').attr('min', minDate.toISOString().split('T')[0]);
            $('#extend-tanggal-baru').attr('max', maxDate.toISOString().split('T')[0]);
            $('#extend-tanggal-baru').val(maxDate.toISOString().split('T')[0]); // Default max

            // Show modal
            new bootstrap.Modal(document.getElementById('modalExtendPeminjaman')).show();

        } catch (err) {
            console.error('Fetch peminjaman error:', err);
            alert('Error loading peminjaman data');
        }
    });

    // Validate date on change
    $('#extend-tanggal-baru').on('change', function() {
        const minDate = new Date($(this).attr('min'));
        const maxDate = new Date($(this).attr('max'));
        const selectedDate = new Date(this.value);

        if (selectedDate < minDate) {
            alert('Tanggal perpanjangan minimal besok!');
            this.value = minDate.toISOString().split('T')[0];
        } else if (selectedDate > maxDate) {
            alert('Perpanjangan maksimal 5 hari dari due date lama!');
            this.value = maxDate.toISOString().split('T')[0];
        }
    });
</script>
