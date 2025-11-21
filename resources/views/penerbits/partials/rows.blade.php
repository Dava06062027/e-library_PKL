@foreach($penerbits as $penerbit)
    <tr data-id="{{ $penerbit->id }}" class="border-bottom border-m365">
        <td>
            <input type="checkbox" class="form-check-input select-penerbit" value="{{ $penerbit->id }}">
        </td>
        <td class="text-m365-blue">{{ $penerbit->nama }}</td>
        <td>{{ $penerbit->alamat ?? '-' }}</td>
        <td>{{ $penerbit->no_telepon ?? '-' }}</td>
        <td>{{ $penerbit->email ?? '-' }}</td>
        <td class="text-center">
            <button class="btn btn-sm btn-outline-info btn-view-detail" data-id="{{ $penerbit->id }}" title="View Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary btn-view-bukus" data-id="{{ $penerbit->id }}" title="View Bukus">
                <i class="bi bi-book"></i>
            </button>
        </td>
    </tr>
@endforeach
