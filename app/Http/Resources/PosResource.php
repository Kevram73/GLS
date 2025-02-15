<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'is_active' => $this->is_active,
            'owner_id' => $this->manager_id,
            'owner' => $this->manager->nom . " " . $this->manager->prenom,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
