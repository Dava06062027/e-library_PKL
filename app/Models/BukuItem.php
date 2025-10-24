<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuItem extends Model
{
    use HasFactory;

    protected $table = 'buku_items';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_buku',
        'kondisi',
        'status',
        'sumber',
        'id_rak',
        'barcode'
    ];

    // Relationship ke bukus
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }

    // Relationship ke raks
    public function rak()
    {
        return $this->belongsTo(Rak::class, 'id_rak');
    }

    // Relationship ke peminjamans
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'id_buku_item');
    }

    // Relationship ke tataraks
    public function tataraks()
    {
        return $this->hasMany(Tatarak::class, 'id_buku_item');
    }
}
