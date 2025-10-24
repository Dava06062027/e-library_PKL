<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $table = 'raks';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'barcode',
        'nama',
        'kolom',
        'baris',
        'kapasitas',
        'id_lokasi',
        'id_kategori'
    ];

    // Relationship ke lokasi_raks
    public function lokasiRak()
    {
        return $this->belongsTo(LokasiRak::class, 'id_lokasi');
    }

    // Relationship ke kategoris
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    // Relationship ke buku_items (1 rak bisa punya banyak buku items)
    public function bukuItems()
    {
        return $this->hasMany(BukuItem::class, 'id_rak');
    }

    // Relationship ke tataraks
    public function tataraks()
    {
        return $this->hasMany(Tatarak::class, 'id_rak');
    }
}
