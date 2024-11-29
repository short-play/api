<?php

declare(strict_types=1);

namespace App\Request\Api\Video;

use Hyperf\Validation\Request\FormRequest;

class VideoFindRequest extends FormRequest
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
            'tagId' => 'integer'
        ];
    }
}
