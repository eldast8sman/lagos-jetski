<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users_id',
            'description' => ['required', 'string', 'min:12'],
            'type' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'orders'   => ['required', 'array'],
            'orders.*.quantity' => ['required', 'integer'],
            'orders.*.g5_id' => ['required', 'string'],
            'orders.*.name' => ['required', 'string'],
            'orders.*.amount' => ['required', 'numeric'],
            'service_charge' => ['required', 'boolean'],
            'tip_amount' => ['numeric']
        ];
    }
}
