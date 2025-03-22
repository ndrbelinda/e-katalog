<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capacities extends Model
{
    protected $table = 'capacities'; // Nama tabel di database
    protected $fillable = [
        'id_produk',
        'besar_kapasitas',
        'tarif_kapasitas',
        'deskripsi_kapasitas',
        'is_verified_kapasitas',
        'tampil_ekatalog',
    ];

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    // Relasi ke RiwayatPricingCapacities
    public function RiwayatPricingKapasitas()
    {
        return $this->hasMany(RiwayatPricingKapasitas::class, 'kapasitas_id');
    }
}
