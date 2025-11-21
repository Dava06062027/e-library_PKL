<div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-m365-gray border-top border-m365">
    <div class="text-secondary small">
        Showing {{ $bukus->firstItem() ?: 0 }} - {{ $bukus->lastItem() ?: 0 }} of {{ $bukus->total() }}
    </div>
    <div>
        {!! $bukus->withQueryString()->links('pagination::simple-bootstrap-5') !!} <!-- Use simple Bootstrap 5 style -->
    </div>
</div>
