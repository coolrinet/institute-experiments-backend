<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'middleName' => $this->middle_name,
            'isAdmin' => $this->when($request->user()->is_admin, $this->is_admin),
            'email' => $this->email,
            'machineryCount' => $this->whenCounted('machineries'),
            'machineryParameterCount' => $this->whenCounted('machineryParameters'),
            'researchCount' => $this->whenCounted('research'),
            'participatoryResearchCount' => $this->whenCounted('participatoryResearch'),
            'experimentCount' => $this->whenCounted('experiments'),
        ];
    }
}
