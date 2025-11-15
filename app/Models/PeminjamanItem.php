<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PeminjamanItem extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_items';

    protected $fillable = [
        'id_peminjaman',
        'id_buku_item',
        'kondisi_pinjam',
        'status_item',
        'tanggal_kembali_aktual',
        'kondisi_kembali',
        'denda_keterlambatan',
        'denda_kerusakan',
        'total_denda_item',
        'catatan_pengembalian',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
        'denda_keterlambatan' => 'decimal:2',
        'denda_kerusakan' => 'decimal:2',
        'total_denda_item' => 'decimal:2',
    ];

    // Relationships
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function bukuItem()
    {
        return $this->belongsTo(BukuItem::class, 'id_buku_item');
    }

    // Helper Methods
    public function getStatusBadgeClass()
    {
        return match($this->status_item) {
            'Dipinjam' => 'bg-warning text-dark',
            'Dikembalikan' => 'bg-success',
            'Hilang' => 'bg-dark',
            'Rusak' => 'bg-danger',
            default => 'bg-primary',
        };
    }
}
