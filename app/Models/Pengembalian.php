<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalians';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_peminjaman', 'id_officer', 'tanggal_kembali_aktual', 'denda_keterlambatan', 'denda_kerusakan', 'total_denda', 'kondisi_kembali', 'catatan'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }
}
