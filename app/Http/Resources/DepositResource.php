<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class DepositResource extends JsonResource
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
            'members' => new MemberResource($this->members),
            'date' => $this->date,
            'debet' => $this->debet,
            'interest' => $this->interest,
            'kredit' => $this->kredit,
            'deposit_types' => new DepositTypesResourse($this->deposit_types),
            'saldo' => $this->saldo,
        ];
    }
}
