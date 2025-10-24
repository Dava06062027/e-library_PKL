<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perpanjangan extends Model
{
    use HasFactory;

    protected $table = 'perpanjangans';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_peminjaman', 'id_officer', 'tanggal_perpanjangan', 'due_date_lama', 'due_date_baru', 'biaya', 'catatan'
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
