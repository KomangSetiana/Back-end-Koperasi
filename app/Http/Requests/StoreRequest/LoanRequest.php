<?php

namespace App\Http\Requests\StoreRequest;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'member_id' => 'required|exists:members,id',
            'loan_amount' => 'required',
            'date' => 'required|date',
            'time_period' => 'numeric|required',
            "created_by" => "nullable|exists:users,id" ?? auth()->user()->id,

        ];
    }
}
