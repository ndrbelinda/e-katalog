<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'faqId' => $this->id,
            'question' => $this->pertanyaan,
            'answer' => $this->jawaban,
        ];
    }
}