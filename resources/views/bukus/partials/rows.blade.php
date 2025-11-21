@foreach($bukus as $buku)
    <tr data-id="{{ $buku->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-buku" value="{{ $buku->id }}">
        </td>
        <td class="text-m365-blue">{{ $buku->judul }}</td>
        <td class="text-secondary">{{ $buku->pengarang }}</td>
        <td>{{ $buku->tahun_terbit }}</td>
        <td><span class="badge bg-info">{{ $buku->kategori?->nama ?? '-' }}</span> / <span class="badge bg-secondary">{{ $buku->subKategori?->nama ?? '-' }}</span></td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $buku->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary btn-view-eksemplar" data-id="{{ $buku->id }}" title="View Eksemplar">
                <i class="bi bi-bookshelf"></i>
            </button>
        </td>
    </tr>
@endforeach

@if($bukus->isEmpty())
    <tr class="no-data">
        <td colspan="6" class="text-center py-5 text-secondary">
            <i class="bi bi-book" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">No bukus found.</p>
        </td>
    </tr>
@endif
