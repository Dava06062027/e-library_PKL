<div class="modal fade" id="modalNewBuku" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="form-new-buku" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-book-half me-2"></i>Create New Buku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input name="judul" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                        <input name="pengarang" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                        <input name="tahun_terbit" type="number" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ISBN</label>
                        <input name="isbn" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Barcode <span class="text-danger">*</span></label>
                        <input name="barcode" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                        <select name="id_penerbit" class="form-select" required>
                            <option value="">Select Penerbit</option>
                            @foreach($penerbits as $penerbit)
                                <option value="{{ $penerbit->id }}">{{ $penerbit->nama ?? $penerbit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">Select Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama ?? $kategori->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sub Kategori <span class="text-danger">*</span></label>
                    <select name="id_sub_kategori" class="form-select" required>
                        <option value="">Select Sub Kategori</option>
                        @foreach($subkategoris as $subkategori)
                            <option value="{{ $subkategori->id }}">{{ $subkategori->nama ?? $subkategori->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>
