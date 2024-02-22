<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabyrinthBlockResource extends JsonResource
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
            'coordinates' => [
                'x' => $this->x,
                'y' => $this->y,
            ],
            'passable' => $this->passable,
        ];
    }

    public function with(Request $request)
    {
        return [
            'status' => 'success',
        ];
    }
}
