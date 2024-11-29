<?php

declare(strict_types=1);

namespace App\Request\Api\CommentLike;

use Hyperf\Validation\Request\FormRequest;

class CommentLikeRequest extends FormRequest
{

    /**
     * @var array|array[]
     */
    protected array $scenes = [
        'unlike' => ['cr_id'],
        'like' => ['cr_id', 'is_dislike']
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
            'cr_id' => 'required|integer',
            'is_dislike' => 'required|boolean'
        ];
    }
}
