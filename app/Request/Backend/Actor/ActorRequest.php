<?php

declare(strict_types=1);

namespace App\Request\Backend\Actor;

use Hyperf\Validation\Request\FormRequest;

class ActorRequest extends FormRequest
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
            'name' => 'required|string|max:20',
        ];
    }
}
