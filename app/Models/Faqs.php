<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    protected $table = 'faqs'; // Nama tabel di database
    protected $fillable = [
        'id_produk',
        'pertanyaan',
        'jawaban',
        'tampil_ekatalog',
    ];

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}