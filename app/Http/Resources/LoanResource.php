<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cutAdmin = $this->loan_amount * 0.015;
        $mandatory = $this->loan_amount * 0.01;
        return [
            'id' => $this->id,
            'members' => $this->members,
            'loan_amount' => $this->loan_amount,
            'date' => $this->date,
            'time_period' => $this->time_period,
            'admin_deduction' => $cutAdmin,
            'mandotory_savings' => $mandatory,
            'loan_obtaneb' => $this->loan_amount - ($cutAdmin + $mandatory),
            'users' => $this->users,
            'installments' => $this->installments

        ];
    }
}
