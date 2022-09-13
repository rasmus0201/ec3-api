<?php

namespace App\Http\Requests;

use App\Models\Sensor;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceMeasurementRequest extends FormRequest
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
            'data' => [
                'required',
                'array'
            ],
            'data.*.t' => [
                'required',
                'string',
                'exists:' . Sensor::class . ',name',
            ],
            'data.*.ts' => [
                'required',
                'integer'
            ],
            'data.*.v' => [
                'required',
                'numeric'
            ],
        ];
    }
}
