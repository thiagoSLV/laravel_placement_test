<?php

namespace App\Http\Requests;

use App\Rules\NotNull;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CompanyUpdateRequest extends FormRequest
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
            'CNPJ' => ['unique:companies,CNPJ','digits:14'],
            'state' => ['alpha', 'size:2']
        ];
    }


    public function message(){
        return [
            'CNPJ.digits' => "formato de CNPJ inválido!!"
        ];
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
