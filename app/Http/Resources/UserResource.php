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
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'num_phone' => $this->num_phone,
            'type_user_id' => $this->type_user_id,
            'email' => $this->email,
            'actif' => $this->actif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'point_of_sale_id' => $this->point_of_sale_id,
            'is_commercial' => $this->is_commercial,
            'stock_journal' => $this->stock_journal
        ];
    }
}
