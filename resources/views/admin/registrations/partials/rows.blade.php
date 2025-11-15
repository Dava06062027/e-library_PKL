@foreach($registrations as $registration)
    <tr data-id="{{ $registration->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-registration" value="{{ $registration->id }}">
        </td>
        <td>
            <div class="d-flex align-items-center">
                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold me-2"
                      style="width: 32px; height: 32px; font-size: 14px;">
                    {{ strtoupper(substr($registration->name, 0, 1)) }}
                </span>
                <span class="text-m365-blue">{{ $registration->name }}</span>
            </div>
        </td>
        <td class="text-secondary">
            {{ $registration->email }}
        </td>
        <td>
            <span class="badge bg-secondary">{{ $registration->temp_card_number ?? '-' }}</span>
        </td>
        <td>
            <span class="badge {{ $registration->getStatusBadgeClass() }} status-badge">
                {{ $registration->getStatusLabel() }}
            </span>
        </td>
        <td class="text-secondary small">
            {{ $registration->created_at->format('d M Y, H:i') }}
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-primary btn-view-detail" data-id="{{ $registration->id }}" title="Lihat Detail">
                <i class="bi bi-eye"></i>
            </button>
        </td>
    </tr>
@endforeach

@if($registrations->isEmpty())
    <tr>
        <td colspan="7" class="text-center py-5 text-secondary">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">Tidak ada pendaftaran ditemukan.</p>
        </td>
    </tr>
@endif

<style>
    .text-m365-blue { color: #0078d4; }
    .border-m365 { border-color: #d1d1d1 !important; }
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
</style>
