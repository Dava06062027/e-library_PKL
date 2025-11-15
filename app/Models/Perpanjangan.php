<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Perpanjangan extends Model
{
    use HasFactory;

    protected $table = 'perpanjangans';

    protected $fillable = [
        'id_peminjaman',
        'id_officer',
        'tanggal_perpanjangan',
        'due_date_lama',
        'due_date_baru',
        'hari_perpanjangan',
        'biaya',
        'catatan',
    ];

    protected $casts = [
        'tanggal_perpanjangan' => 'date',
        'due_date_lama' => 'date',
        'due_date_baru' => 'date',
        'biaya' => 'decimal:2',
    ];

    // Relationships
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }
}
