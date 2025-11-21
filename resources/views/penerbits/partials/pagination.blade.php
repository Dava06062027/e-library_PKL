<!-- resources/views/penerbits/partials/pagination.blade.php -->

<div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-m365-gray border-top border-m365">
    <div class="text-secondary small">
        Showing {{ $penerbits->firstItem() ?: 0 }} - {{ $penerbits->lastItem() ?: 0 }} of {{ $penerbits->total() }}
    </div>
    <div>
        {!! $penerbits->withQueryString()->links('pagination::simple-bootstrap-5') !!}
    </div>
</div>