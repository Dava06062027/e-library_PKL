<div class="modal fade" id="modalEditBuku" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-edit-buku" class="modal-content">
            @csrf
            <input type="hidden" id="edit-buku-id" name="id">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input id="edit-judul" name="judul" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                        <input id="edit-pengarang" name="pengarang" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                        <input id="edit-tahun-terbit" name="tahun_terbit" type="number" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ISBN</label>
                        <input id="edit-isbn" name="isbn" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Barcode <span class="text-danger">*</span></label>
                        <input id="edit-barcode" name="barcode" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                        <select id="edit-id-penerbit" name="id_penerbit" class="form-select" required>
                            <option value="">Select Penerbit</option>
                            @foreach($penerbits as $penerbit)
                                <option value="{{ $penerbit->id }}">{{ $penerbit->nama ?? $penerbit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select id="edit-id-kategori" name="id_kategori" class="form-select" required>
                            <option value="">Select Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama ?? $kategori->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sub Kategori <span class="text-danger">*</span></label>
                    <select id="edit-id-sub-kategori" name="id_sub_kategori" class="form-select" required>
                        <option value="">Select Sub Kategori</option>
                        @foreach($subkategoris as $subkategori)
                            <option value="{{ $subkategori->id }}">{{ $subkategori->nama ?? $subkategori->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
