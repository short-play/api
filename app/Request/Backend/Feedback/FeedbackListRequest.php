<?php

declare(strict_types=1);

namespace App\Request\Backend\Feedback;

use App\Constants\Enum\FeedbackStatus;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class FeedbackListRequest extends FormRequest
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
            'status' => [Rule::in(FeedbackStatus::values())],
            'title' => 'string|max:30',
        ];
    }
}
