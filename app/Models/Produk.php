<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = ['nama_produk', 'deskripsi_produk', 'level_produk'];

    //relasi ke perangkats
    public function perangkats()
    {
        return $this->hasMany(Perangkats::class, 'id_produk');
    }

    //relasi ke faqs
    public function faqs()
    {
        return $this->hasMany(Faqs::class, 'id_produk');
    }

    //relasi ke kapasitas
    public function capacities (){
        return $this->hasMany(Capacities::class, 'id_produk');
    }
}
