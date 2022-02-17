<?php

namespace App\Http\Requests;

use App\Rules\NotNull;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class SupplierUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [new NotNull],
            'CPF' => ['unique:suppliers,CPF', 'digits:11'],
            'CNPJ' => ['unique:suppliers,CNPJ', 'digits:14'],
            'RG' => ['unique:suppliers,RG', 'digits:9'],
            'state' => ['alpha', 'size:2'],
            'birth_date' => ['date', 'date_format:Y-m-d'],
            'phone_numbers' => ['JSON']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (Arr::has(request()->all(), ['CPF', 'CNPJ']))
                $validator->errors()->add('CPF_CNPJ', 'Por favor insira apenas CPF ou CNPJ');
        });
    }

    public function failedValidation(Validator $validator){
        $response = response()->json([
            'message' => 'Dados inválidos',
            'errors' => $validator->errors()->messages(),
            'failed' => $validator->failed()
        ], 422);

        throw new HttpResponseException($response);
    }
}
