<?php

namespace App\Http\Requests;

use App\Traits\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordRequest extends FormRequest
{
    use Response;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "old_password" => "required|" . function () {
                return Hash::check($this->old_password, $this->user()->password)
                    ? true
                    : false;
            },
            "new_password" => "required|min:6",
            "new_password_confirmation" => "required|same:new_password",
        ];
    }
    public function failedValidation($validator)
    {
        throw new HttpResponseException($this->validationError($validator));
    }
}
