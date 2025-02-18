<?php

namespace App\Http\Resources;

use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositTypesResourse extends JsonResource
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
            'deposit_name' => $this->deposit_name
        ];
    }
}
