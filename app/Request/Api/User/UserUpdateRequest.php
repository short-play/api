<?php

declare(strict_types=1);

namespace App\Request\Api\User;

use Hyperf\Validation\Request\FormRequest;

class UserUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|alpha_dash|max:15',
            'sex' => 'nullable|integer|between:1,2',
            'birthday' => 'nullable|date',
            'personal_sign' => 'nullable|string|max:40',
            'profile' => 'nullable|string|max:255',
        ];
    }
}
