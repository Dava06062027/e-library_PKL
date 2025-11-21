<!-- resources/views/sub_kategoris/partials/rows.blade.php -->

@foreach($subkategoris as $subkategori)
    <tr data-id="{{ $subkategori->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-subkategori" value="{{ $subkategori->id }}">
        </td>
        <td class="text-m365-blue">{{ $subkategori->nama }}</td>
        <td><span class="badge bg-info">{{ $subkategori->kategori?->nama ?? '-' }}</span></td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $subkategori->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary btn-view-bukus" data-id="{{ $subkategori->id }}" title="View Bukus">
                <i class="bi bi-book"></i>
            </button>
        </td>
    </tr>
@endforeach

@if($subkategoris->isEmpty())
    <tr class="no-data">
        <td colspan="4" class="text-center py-5 text-secondary">
            <i class="bi bi-tags" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">No sub kategoris found.</p>
        </td>
    </tr>
@endif
