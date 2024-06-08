<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

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
            'lastExperimentDate' => $this->last_experiment_date ? Date::parse($this->last_experiment_date)->translatedFormat('j F Y') : null,
            'isPublic' => $this->is_public,
            'machinery' => MachineryResource::make($this->whenLoaded('machinery')),
            'author' => UserResource::make($this->whenLoaded('author')),
            'participants' => UserResource::collection($this->whenLoaded('participants')),
            'experimentCount' => $this->whenCounted('experiments'),
        ];
    }
}
