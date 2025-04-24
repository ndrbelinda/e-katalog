<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CapacityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'capacityId' => $this->id,
            'capacitySize' => $this->besar_kapasitas,
            'price' => $this->harga_terbaru, // Sesuaikan dengan field yang ada
        ];
    }
}