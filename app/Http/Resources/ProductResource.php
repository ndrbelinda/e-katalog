<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'productId' => $this->id,
            'productName' => $this->nama_produk,
            'productDescription' => $this->deskripsi_produk,
            'devices' => DeviceResource::collection($this->perangkats),
            'capacities' => CapacityResource::collection($this->capacities),
            'faqs' => FaqResource::collection($this->faqs),
        ];
    }
}