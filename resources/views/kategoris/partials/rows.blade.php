@foreach($kategoris as $kategori)
    <tr data-id="{{ $kategori->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-kategori" value="{{ $kategori->id }}">
        </td>
        <td class="text-m365-blue">{{ $kategori->nama }}</td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $kategori->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary btn-view-subkategoris" data-id="{{ $kategori->id }}" title="View Sub Kategoris">
                <i class="bi bi-tags"></i>
            </button>
        </td>
    </tr>
@endforeach
