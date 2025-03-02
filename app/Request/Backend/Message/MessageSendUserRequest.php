<?php

declare(strict_types=1);

namespace App\Request\Backend\Message;

use Hyperf\Validation\Request\FormRequest;

class MessageSendUserRequest extends FormRequest
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
            'id' => 'required|integer',
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|integer|distinct',
        ];
    }
}
