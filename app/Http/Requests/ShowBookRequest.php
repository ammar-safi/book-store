<?php

namespace App\Http\Requests;

use App\Traits\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use function PHPUnit\Framework\returnSelf;

class ShowBookRequest extends FormRequest
{
    use Response;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id" => [
                'required',
                'string',
                'exists:books,uuid',
                function ($attribute, $value, $fail) {
                    if (!$this->user("api")->books()->where('uuid', $value)->exists()) {
                        $fail('This book does not belong to you.');
                    }
                }
            ]
        ];
    }

    public function failedValidation($validator)
    {
        throw new HttpResponseException($this->validationError($validator));
    }
}
