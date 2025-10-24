<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_member', 'id_buku_item', 'id_officer', 'tanggal_pinjam', 'tanggal_kembali_rencana', 'status', 'catatan'
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'id_member');
    }

    public function bukuItem()
    {
        return $this->belongsTo(BukuItem::class, 'id_buku_item');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_peminjaman');
    }

    public function perpanjangans()
    {
        return $this->hasMany(Perpanjangan::class, 'id_peminjaman');
    }
}
