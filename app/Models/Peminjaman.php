<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'transaction_number',
        'id_member',
        'id_officer',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'status_transaksi',
        'total_items',
        'items_dikembalikan',
        'total_denda',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'total_denda' => 'decimal:2',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(User::class, 'id_member');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }

    public function items()
    {
        return $this->hasMany(PeminjamanItem::class, 'id_peminjaman');
    }

    public function perpanjangans()
    {
        return $this->hasMany(Perpanjangan::class, 'id_peminjaman');
    }

    // Helper Methods
    public function getStatusBadgeClass()
    {
        return match($this->status_transaksi) {
            'Aktif' => 'bg-warning text-dark',
            'Selesai' => 'bg-success',
            'Dibatalkan' => 'bg-secondary',
            default => 'bg-primary',
        };
    }

    public function getDaysLateAttribute()
    {
        if ($this->status_transaksi !== 'Aktif') return 0;

        $dueDate = Carbon::parse($this->tanggal_kembali_rencana);
        $today = Carbon::today();

        return max(0, $today->diffInDays($dueDate, false) * -1);
    }

    public function isOverdue()
    {
        return $this->days_late > 0;
    }

    public function calculateLateFee()
    {
        return $this->days_late * 1000; // Rp 1,000 per hari per item
    }
}
