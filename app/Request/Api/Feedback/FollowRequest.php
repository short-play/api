<?php

declare(strict_types=1);

namespace App\Request\Api\Feedback;

use Hyperf\Validation\Request\FormRequest;

class FollowRequest extends FormRequest
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
            'title' => 'required|string|max:30',
            'desc' => 'required|string|max:500',
            'pic_json' => 'array|max:4',
            'pic_json.*' => 'string',
            'link' => 'required|max:50'
        ];
    }
}
