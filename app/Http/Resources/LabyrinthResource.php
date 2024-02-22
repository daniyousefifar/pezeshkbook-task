<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class LabyrinthResource extends JsonResource
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
            'dimensions' => $this->dimensions,
            'start' => is_null($this->start) ? null : $this->start,
            'end' => is_null($this->end) ? null : $this->end,
            'blocks' => LabyrinthBlockResource::collection($this->blocks()->get()),
            'user_id' => $this->user_id,
            'created_at' => [
                'iso' => Carbon::parse($this->created_at)->toIso8601String(),
                'timestamp' => Carbon::parse($this->created_at)->getTimestamp(),
            ],
            'updated_at' => [
                'iso' => Carbon::parse($this->updated_at)->toIso8601String(),
                'timestamp' => Carbon::parse($this->updated_at)->getTimestamp(),
            ],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'status' => 'success',
        ];
    }
}
