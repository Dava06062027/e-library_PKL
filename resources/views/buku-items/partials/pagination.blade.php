<div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-m365-gray border-top border-m365">
    <div class="text-secondary small">
        Showing {{ $items->firstItem() ?: 0 }} - {{ $items->lastItem() ?: 0 }} of {{ $items->total() }}
    </div>
    <div>
        {!! $items->withQueryString()->links('pagination::simple-bootstrap-5') !!}
    </div>
</div>
