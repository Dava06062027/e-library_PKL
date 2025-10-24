<div class="modal fade" id="modalReturnPeminjaman" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-return-peminjaman" class="modal-content">
            @csrf
            <input type="hidden" name="id_peminjaman" id="return-id-peminjaman">
            <div class="modal-header">
                <h5 class="modal-title">Pengembalian Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                    <input type="date" name="tanggal_kembali_aktual" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Kondisi Kembali</label>
                    <select name="kondisi_kembali" class="form-select" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik">Baik</option>
                        <option value="Cukup">Cukup</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Denda Kerusakan</label>
                    <input type="number" name="denda_kerusakan" class="form-control" value="0" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea name="catatan" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
