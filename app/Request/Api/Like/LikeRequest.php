<?php

declare(strict_types=1);

namespace App\Request\Api\Like;

use Hyperf\Validation\Request\FormRequest;

class LikeRequest extends FormRequest
{

    protected array $scenes = [
        'liked' => ['video_id', 'item_id'],
        'like' => ['video_id', 'item_id', 'is_cancel'],
    ];

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
            'is_cancel' => 'required|boolean',
        ];
    }
}
