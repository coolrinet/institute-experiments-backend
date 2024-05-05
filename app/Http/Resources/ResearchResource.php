<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'lastExperimentDate' => $this->last_experiment_date,
            'isPublic' => $this->when($this->author_id === $request->user()->id, $this->is_public),
            'machinery' => MachineryResource::make($this->whenLoaded('machinery')),
            'parameters' => MachineryParameterResource::collection($this->whenLoaded('parameters')),
            'author' => UserResource::make($this->whenLoaded('author')),
            'participants' => UserResource::collection($this->whenLoaded('participants')),
        ];
    }
}
