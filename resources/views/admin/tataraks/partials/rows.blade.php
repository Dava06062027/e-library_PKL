@foreach($tataraks as $index => $tatarak)
    @php
        $colors = ['primary', 'danger', 'info', 'success', 'warning', 'secondary'];
        $avatarColor = $colors[$index % count($colors)];
        $initial = strtoupper(substr($tatarak->user?->name ?? 'U', 0, 1));
    @endphp
    <tr data-id="{{ $tatarak->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox"
                   class="form-check-input select-tatarak"
                   value="{{ $tatarak->id }}">
        </td>
        <td>{{ $tatarak->id }}</td>
        <td>
            <div>
                <strong>{{ $tatarak->bukuItem?->buku?->judul ?? 'N/A' }}</strong><br>
                <small class="text-muted">Barcode: {{ $tatarak->bukuItem?->barcode ?? 'N/A' }}</small>
            </div>
        </td>
        <td>
            <span class="badge bg-primary">{{ $tatarak->rak?->nama ?? 'N/A' }}</span>
            <br>
            <small class="text-muted">{{ $tatarak->rak?->lokasi?->ruang ?? '' }}</small>
        </td>
        <td>
            <span class="badge bg-info">
                Kol: {{ $tatarak->kolom }}, Bar: {{ $tatarak->baris }}
            </span>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <span class="bg-{{ $avatarColor }} text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold me-2"
                      style="width: 32px; height: 32px; font-size: 14px;">
                    {{ $initial }}
                </span>
                <span class="text-m365-blue">{{ $tatarak->user?->name ?? 'Unknown' }}</span>
            </div>
        </td>
        <td>
            @if($tatarak->updated_at)
                {{ $tatarak->updated_at->format('d M Y H:i') }}
            @elseif($tatarak->created_at)
                {{ $tatarak->created_at->format('d M Y H:i') }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
    </tr>
@endforeach

@if($tataraks->isEmpty())
    <tr>
        <td colspan="7" class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mb-0 mt-2">No records found.</p>
        </td>
    </tr>
@endif
