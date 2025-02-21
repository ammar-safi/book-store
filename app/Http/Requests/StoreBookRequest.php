<?php

namespace App\Http\Requests;

use App\Traits\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookRequest extends FormRequest
{
    use Response;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string",
            "author" => "nullable|string",
            "description" => "nullable|string",
        ];
    }

    public function failedValidation($validator)
    {
        throw new HttpResponseException($this->validationError($validator));
    }
}
