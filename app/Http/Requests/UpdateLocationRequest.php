<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'string|min:1|max:255',
            'lat' => 'numeric|between:-90.0,90.0',
            'long' => 'numeric|between:-180.0,180.0',
            'timezone' => [
                'string',
                Rule::in(timezone_identifiers_list())
            ],
        ];
    }
}
