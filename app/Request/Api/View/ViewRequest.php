<?php

declare(strict_types=1);

namespace App\Request\Api\View;

use Hyperf\Validation\Request\FormRequest;

class ViewRequest extends FormRequest
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
            'video_id' => 'required|integer',
            'item_id' => 'required|integer',
            'duration' => 'required|integer',
        ];
    }
}
