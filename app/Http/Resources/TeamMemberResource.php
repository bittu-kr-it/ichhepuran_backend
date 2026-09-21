<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'bio' => $this->bio,
            'photo' => $this->getFirstMediaUrl('photo') ?: null,
            'photoAlt' => $this->photo_alt,
            'group' => $this->group,
            'order' => $this->order,
        ];
    }
}
