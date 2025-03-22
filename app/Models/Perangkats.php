<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perangkats extends Model
{
    protected $table = 'perangkats';
    protected $fillable = ['id_produk', 'gambar_perangkat', 'tarif'];

    public function riwayatPricing()
    {
        return $this->hasMany(RiwayatPricingPerangkat::class, 'perangkat_id');
    }
}
