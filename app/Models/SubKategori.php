<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategori extends Model
{
    protected $table='sub_kategoris';
    public $timestamps=false;
    protected $fillable=['nama', 'id_kategori'];

    public function kategori(){
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function buku()
    {
        return $this->hasMany(Buku::class, 'id_sub_kategori');
    }
}
