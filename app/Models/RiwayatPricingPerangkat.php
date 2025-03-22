<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPricingPerangkat extends Model
{
    protected $table = 'riwayat_pricing_perangkat';
    protected $fillable = ['perangkat_id', 'pricing'];

    public function perangkat()
    {
        return $this->belongsTo(Perangkats::class, 'perangkat_id');
    }
}
