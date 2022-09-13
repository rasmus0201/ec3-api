<?php

namespace App\Http\Requests;

use App\Models\{Location, Sensor};
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
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
            'name' => 'required|string|min:1|max:255',
            'location_id' => 'integer|exists:' . Location::class . ',id',
            'sensor_ids' => 'array',
            'sensor_ids.*' => 'nullable|integer|distinct|exists:' . Sensor::class . ',name',
        ];
    }
}
