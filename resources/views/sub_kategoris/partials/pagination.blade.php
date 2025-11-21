<!-- resources/views/sub_kategoris/partials/pagination.blade.php -->

<div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-m365-gray border-top border-m365">
    <div class="text-secondary small">
        Showing {{ $subkategoris->firstItem() ?: 0 }} - {{ $subkategoris->lastItem() ?: 0 }} of {{ $subkategoris->total() }}
    </div>
    <div>
        {!! $subkategoris->withQueryString()->links('pagination::simple-bootstrap-5') !!}
    </div>
</div>
