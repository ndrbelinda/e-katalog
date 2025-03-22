<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPricingKapasitas extends Model
{
    protected $table = 'riwayat_pricing_kapasitas'; // Nama tabel di database
    protected $fillable = ['kapasitas_id', 'pricing'];

    // Relasi ke Capacities
    public function capacity()
    {
        return $this->belongsTo(Capacities::class, 'kapasitas_id');
    }
}
