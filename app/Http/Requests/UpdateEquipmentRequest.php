<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'equipment_type_id' => 'required|integer|exists:equipment_types,id',
            'serial_number' => 'required|string',
            'desc' => 'nullable|string',
        ];
    }

    /**
     * @param Validator $validator
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator):HttpResponseException {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
