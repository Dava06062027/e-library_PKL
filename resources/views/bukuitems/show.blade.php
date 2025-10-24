@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Buku Item</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><td>{{ $bukuitem->id }}</td></tr>
                <tr>
                    <th>Buku</th>
                    <td>
                        {{ $bukuitem->buku?->judul ?? '-' }} <br>
                        Pengarang: {{ $bukuitem->buku?->pengarang ?? '-' }} <br>
                        Tahun Terbit: {{ $bukuitem->buku?->tahun_terbit ?? '-' }} <br>
                        ISBN: {{ $bukuitem->buku?->isbn ?? '-' }} <br>
                        Barcode Buku: {{ $bukuitem->buku?->barcode ?? '-' }}
                    </td>
                </tr>
                <tr><th>Barcode Item</th><td>{{ $bukuitem->barcode }}</td></tr>
                <tr><th>Kondisi</th><td>{{ ucfirst($bukuitem->kondisi) }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst($bukuitem->status) }}</td></tr>
                <tr><th>Sumber</th><td>{{ $bukuitem->sumber }}</td></tr>

                @if($bukuitem->rak)
                    <tr>
                        <th>Rak</th>
                        <td>
                            {{ $bukuitem->rak->nama }} (Barcode: {{ $bukuitem->rak->barcode }})<br>
                            Kolom: {{ $bukuitem->rak->kolom }}, Baris: {{ $bukuitem->rak->baris }}<br>
                            Kapasitas: {{ $bukuitem->rak->kapasitas }}
                        </td>
                    </tr>
                    <tr>
                        <th>Lokasi Rak</th>
                        <td>{{ $bukuitem->rak->lokasiRak?->ruang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kategori Rak</th>
                        <td>{{ $bukuitem->rak->kategori?->nama ?? '-' }}</td>
                    </tr>
                @else
                    <tr>
                        <th>Rak</th>
                        <td><span class="badge bg-warning">Belum ditempatkan di rak</span></td>
                    </tr>
                @endif
            </table>

            <div class="mt-3">
                <a href="{{ route('bukuitems.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @can('update', $bukuitem)
                    <a href="{{ route('bukuitems.edit', $bukuitem->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection
