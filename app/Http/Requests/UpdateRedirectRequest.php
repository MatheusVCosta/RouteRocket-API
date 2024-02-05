<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateRedirectRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

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
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {   
        $errors = $validator->errors();
        dd($errors);
        $response = new JsonResponse([
            'errors' => $errors,
        ], 422);

        throw new HttpResponseException($response);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'url_target' => 'url:https|unique:redirects',
            'status'     => 'boolean'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
           'url_target.url'      => 'A URL informada precisa ter o protocolo HTTPS.',
           'url_target.unique'   => 'Já existe um redirect com essa URL.',
           'status.boolean'      => 'O valor do status só pode ser verdadeiro ou false.'
        ];
    }
}
