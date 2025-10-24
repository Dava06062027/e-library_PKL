// Directory: resources/views/admin/peminjamans/partials/new-modal.blade.php
<div class="modal fade" id="modalNewPeminjaman" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="form-new-peminjaman" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Peminjaman Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Member</label>
                    <select name="id_member" class="form-select" required>
                        <option value="">-- Pilih Member --</option>
                        @foreach(\App\Models\User::where('role', 'Member')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Buku & Eksemplar</label>
                    <button id="btn-add-buku" type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSelectBuku">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Buku</span>
                    </button>
                </div>
                <div id="selected-books-container" class="mb-4" style="display:none;">
                    <h6 class="fw-bold mb-3">Buku & Eksemplar Terpilih:</h6>
                    <div id="selected-books-list" class="border rounded p-3 bg-light">
                        <!-- Populated dynamically -->
                    </div>
                    <div class="mt-2 text-muted">
                        <small>Total Eksemplar: <span id="total-eksemplar-count">0</span></small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
                    <input type="date" name="tanggal_kembali_rencana" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
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
