<!-- resources/views/raks/partials/rows.blade.php - Add button -->

@foreach($raks as $rak)
    <tr data-id="{{ $rak->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-rak" value="{{ $rak->id }}">
        </td>
        <td class="text-m365-blue">{{ $rak->nama }}</td>
        <td>{{ $rak->barcode }}</td>
        <td>{{ $rak->kolom }}</td>
        <td>{{ $rak->baris }}</td>
        <td>{{ $rak->kapasitas }}</td>
        <td>{{ $rak->lokasi?->ruang ?? '-' }}</td>
        <td><span class="badge bg-info">{{ $rak->kategori?->nama ?? '-' }}</span></td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $rak->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary btn-view-items" data-id="{{ $rak->id }}" title="View Items">
                <i class="bi bi-bookshelf"></i>
            </button>
        </td>
    </tr>
@endforeach
