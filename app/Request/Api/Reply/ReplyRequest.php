<?php

declare(strict_types=1);

namespace App\Request\Api\Reply;

use Hyperf\Validation\Request\FormRequest;

class ReplyRequest extends FormRequest
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
            'comment_id' => 'required|integer',
            'reply_id' => 'required|integer',
            'content' => 'required|string|max:255',
        ];
    }
}
