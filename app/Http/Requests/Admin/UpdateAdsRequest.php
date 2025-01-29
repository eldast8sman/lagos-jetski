<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdsRequest extends FormRequest
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
            'campaign_name' => 'string|required',
            'description' => 'string|nullable',
            'type' => 'required|string|in:Primary,Secondary',
            'ads_link' => 'required|string',
            'image_banner' => 'file|mimes:png,jpg,jpeg,gif|nullable',
            'campaign_start' => 'date|nullable',
            'campaign_end' => 'date|nullable'
        ];
    }
}
