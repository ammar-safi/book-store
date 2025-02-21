<?php

namespace App\Http\Requests;

use App\Traits\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use Response;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // \Log::info();
        return [
            "name" => "required|string|min:3|max:255",
            "email" => ["required", "email", Rule::unique('users' , "email")->ignore(auth("api")->id())],
        ];
    }

    public function failedValidation($validator)
    {
        throw new HttpResponseException($this->validationError($validator));
    }
}
