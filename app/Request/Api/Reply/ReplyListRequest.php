<?php

declare(strict_types=1);

namespace App\Request\Api\Reply;

use Hyperf\Validation\Request\FormRequest;

class ReplyListRequest extends FormRequest
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
            'not_ids' => 'array',
            'not_ids.*' => 'integer',
        ];
    }
}
