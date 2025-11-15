<div class="d-flex justify-content-between align-items-center">
    <div class="text-secondary small">
        Showing {{ $registrations->firstItem() ?: 0 }} - {{ $registrations->lastItem() ?: 0 }} of {{ $registrations->total() }}
    </div>
    <div>{!! $registrations->links() !!}</div>
</div>
