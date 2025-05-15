<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|required|max:255',
            'email' => 'string|required|unique:users|email',
            'password' => [
                'string',
                'required',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/',
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . Carbon::now()->subYears(16)->toDateString(),
            ],
            'phone_number' => 'required|string|unique:users|regex:/^07[56789]\d{7}$/',
        ];
    }
}
