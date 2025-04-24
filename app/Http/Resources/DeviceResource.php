<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'deviceId' => $this->id,
            'deviceName' => $this->jenis_perangkat,
            'deviceImage' => $this->gambar_perangkat, // Sesuaikan dengan field yang ada
            'price' => $this->harga_terbaru, // Sesuaikan dengan field yang ada
        ];
    }
}