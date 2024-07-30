<?php

namespace App\Http\Requests\StoreRequest;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd($this->debet);

        return [
            "member_id" => "required|exists:members,id",
            "date" => "required|date",
            "debet" => "nullable|numeric",
            "kredit" =>  "nullable|numeric",
            "saldo" => "numeric",
            "deposit_type_id" => "required|exists:deposit_types,id",
            "created_by" => "nullable|exists:users,id" ?? auth()->user()->id,
        ];
    }
}
