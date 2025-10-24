// Directory: resources/views/admin/peminjamans/partials/pagination.blade.php
<div class="d-flex justify-content-between align-items-center">
    <div>Showing {{ $peminjamans->firstItem() ?: 0 }} - {{ $peminjamans->lastItem() ?: 0 }} of {{ $peminjamans->total() }}</div>
    <div>{!! $peminjamans->links() !!}</div>
</div>
