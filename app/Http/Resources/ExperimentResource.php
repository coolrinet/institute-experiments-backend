<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class ExperimentResource extends JsonResource
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
            'date' => Date::parse($this->date)->translatedFormat('d F Y'),
            'research' => ResearchResource::make($this->whenLoaded('research')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'quantitativeInputs' => ExperimentParameterResource::collection(
                $this->whenLoaded('quantitativeInputs')
            ),
            'qualityInputs' => ExperimentParameterResource::collection(
                $this->whenLoaded('qualityInputs')
            ),
            'quantitativeOutputs' => ExperimentParameterResource::collection(
                $this->whenLoaded('quantitativeOutputs')
            ),
            'qualityOutputs' => ExperimentParameterResource::collection(
                $this->whenLoaded('qualityOutputs')
            ),
        ];
    }
}
