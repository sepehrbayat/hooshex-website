<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for OrderItem model
 * Version 1
 */
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderable_type' => $this->orderable_type,
            'orderable_id' => $this->orderable_id,
            'price' => (int) $this->price,
            'quantity' => $this->quantity,
            'orderable' => $this->whenLoaded('orderable'),
        ];
    }
}

