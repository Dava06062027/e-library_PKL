// Directory: resources/views/admin/peminjamans/partials/extend-modal.blade.php
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
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Rencana Baru</label>
                    <input type="date" name="tanggal_kembali_rencana_baru" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Biaya (jika ada)</label>
                    <input type="number" name="biaya" class="form-control" value="0" min="0">
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
