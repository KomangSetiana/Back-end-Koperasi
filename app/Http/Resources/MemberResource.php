<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class MemberResource extends JsonResource
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
            'korwils' => new KorwilResource($this->korwil),
            'gender' => $this->gender,
            'address' => $this->address,
            'telp' => $this->telp,
            'user_id' => $this->user_id
            // 'deposits' => new DepositResource($this->deposits)
        ];
    }
}
