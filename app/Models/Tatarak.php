<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tatarak extends Model
{
    use HasFactory;

    protected $table = 'tataraks';
    protected $fillable = ['id_buku_item', 'id_rak', 'kolom', 'baris', 'id_user'];

    // ✅ Enable timestamps
    public $timestamps = true;

    public function bukuItem()
    {
        return $this->belongsTo(BukuItem::class, 'id_buku_item');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'id_rak');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
