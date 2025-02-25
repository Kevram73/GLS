<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenteResource extends JsonResource
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
            "date" => $this->date,
            "montant" => $this->montant,
            "point_of_sale_id" => $this->point_of_sale_id,
            "client_id" => $this->client_id,
            "journal_id" => $this->journal_id,
            "nbre" => $this->nbre,
            "seller_id" => $this->seller_id,
            "is_paid" => $this->is_paid,
            "point_of_sale" => PosResource::collection($this->pointOfSale),
            "seller" => UserResource::collection($this->seller),
            "client" => UserResource::collection($this->client)
        ];
    }
}
