<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class SupplierStorePostRequest extends FormRequest
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
            'name' => ['required'],
            'CNPJ' => ['required_without:CPF', 'unique:suppliers,CNPJ','digits:14'],
            'CPF' => ['required_without:CNPJ','unique:suppliers,CPF','digits:11'],
            'RG' => ['required_with:CPF','unique:suppliers,RG','digits:9'],
            'birth_date' => ['required_with:CPF', 'date', 'date_format:Y-m-d'],
            'state' => ['required','alpha', 'size:2'],
            'phone_numbers' => ['required', 'JSON']
        ];
    }


    public function message(){
        return [
            'CNPJ.digits' => "formato de CNPJ inválido!!",
            'CPF.digits' => "formato de CPF inválido!!",
            'RG.digits' => "formato de RG inválido!!",
        ];
    }

    public function withValidator($validator)
    {
        $keys = [
            'RG' => 'RG',
            'birth_date' => 'Data de aniversário'
        ];
        $validator->after(function ($validator) use ($keys){
            if (Arr::has(request()->all(), ['CPF', 'CNPJ']))
                $validator->errors()->add('CPF_CNPJ', 'Por favor insira apenas CPF ou CNPJ');
            foreach($keys as $key => $value){
                if (request()->get('CNPJ') && request()->get($key))
                    $validator->errors()->add($key, "CNPJ não deve conter {$value}");
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Dados inválidos',
            'errors' => $validator->errors()->messages(),
            'failed' => $validator->failed()
        ], 422);

        throw new HttpResponseException($response);
    }
}
