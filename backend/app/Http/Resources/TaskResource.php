<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'origin' => new OriginResource($this->whenLoaded('origin')),
            'destination' => new DestinationResource($this->whenLoaded('destination')),
            'schedule_expression' => $this->schedule_expression,
            'is_active' => $this->is_active,
            'last_executed_at' => $this->last_executed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
