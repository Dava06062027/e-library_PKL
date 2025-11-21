<!-- resources/views/kategoris/partials/pagination.blade.php -->

<div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-m365-gray border-top border-m365">
    <div class="text-secondary small">
        Showing {{ $kategoris->firstItem() ?: 0 }} - {{ $kategoris->lastItem() ?: 0 }} of {{ $kategoris->total() }}
    </div>
    <div>
        {!! $kategoris->withQueryString()->links('pagination::simple-bootstrap-5') !!}
    </div>
</div>