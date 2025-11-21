<!-- resources/views/sub_kategoris/partials/subkategori-detail-modal.blade.php -->

<div class="modal fade" id="modalSubkategoriDetail" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-tag me-2"></i>Detail Sub Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <tr><th width="30%">Nama</th><td id="detail-nama">-</td></tr>
                    <tr><th>Kategori</th><td id="detail-kategori">-</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
