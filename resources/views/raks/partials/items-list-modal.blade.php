<!-- resources/views/raks/partials/items-list-modal.blade.php -->

<div class="modal fade" id="modalItemsList" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bookshelf me-2"></i>List Buku Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Sumber</th>
                            <th>Buku</th>
                        </tr>
                        </thead>
                        <tbody id="items-table-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
