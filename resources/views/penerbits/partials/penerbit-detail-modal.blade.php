<!-- resources/views/penerbits/partials/penerbit-detail-modal.blade.php -->

<div class="modal fade" id="modalPenerbitDetail" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Detail Penerbit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <tr><th width="30%">Nama</th><td id="detail-nama">-</td></tr>
                    <tr><th>Alamat</th><td id="detail-alamat">-</td></tr>
                    <tr><th>No Telepon</th><td id="detail-no-telepon">-</td></tr>
                    <tr><th>Email</th><td id="detail-email">-</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>