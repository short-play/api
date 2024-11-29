<?php

declare(strict_types=1);

namespace App\Request\Api\Message;

use App\Constants\Enum\MessageType;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class MessageDeleteRequest extends FormRequest
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
            'type' => ['required', Rule::in(array_map(fn($v) => $v->value, MessageType::cases()))],
        ];
    }
}
