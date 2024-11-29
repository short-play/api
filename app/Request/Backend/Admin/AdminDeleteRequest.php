<?php

declare(strict_types=1);

namespace App\Request\Backend\Admin;

use App\Kernel\BaseRequest;

class AdminDeleteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->adminAuthorization();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'disable' => 'required|boolean',
            'ids' => 'required|array|max:10',
            'ids.*' => 'integer|distinct',
        ];
    }
}
