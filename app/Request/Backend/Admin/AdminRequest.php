<?php

declare(strict_types=1);

namespace App\Request\Backend\Admin;

use App\Constants\Enum\AdminRole;
use App\Kernel\BaseRequest;
use Hyperf\Validation\Rule;

class AdminRequest extends BaseRequest
{

    /**
     * The scenes defined by developer.
     */
    public array $scenes = [
        'update' => ['mail', 'name', 'role'],
        'create' => ['mail', 'name', 'role', 'password'],
    ];

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
            'mail' => 'required|email|string|max:40',
            'name' => 'required|string|max:15',
            'password' => 'required|string',
            'role' => ['required', 'integer', Rule::in(array_map(fn($v) => $v->value, AdminRole::cases()))],
        ];
    }
}
