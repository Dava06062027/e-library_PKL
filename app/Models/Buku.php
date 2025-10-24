<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'bukus';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'pengarang',
        'tahun_terbit',
        'isbn',
        'barcode',
        'id_penerbit',
        'id_kategori',
        'id_sub_kategori'
    ];

    // Relationship ke penerbits
    public function penerbit()
    {
        return $this->belongsTo(Penerbit::class, 'id_penerbit');
    }

    // Relationship ke kategoris
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    // Relationship ke sub_kategoris
    public function subKategori()
    {
        return $this->belongsTo(SubKategori::class, 'id_sub_kategori');
    }

    // Relationship ke buku_items (1 buku bisa punya banyak eksemplar)
    public function Items()
    {
        return $this->hasMany(BukuItem::class, 'id_buku');
    }
}
