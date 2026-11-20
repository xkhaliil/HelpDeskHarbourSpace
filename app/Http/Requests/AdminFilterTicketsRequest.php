<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminFilterTicketsRequest extends FormRequest
{
    /**
     * Where to go when a filter is invalid (instead of the previous page).
     *
     * @var string
     */
    protected $redirectRoute = 'admin.tickets.index';

    /**
     * Only admins reach this request, the admin middleware already checks that.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(TicketStatus::values())],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'priority' => ['nullable', Rule::in(TicketPriority::values())],
            'role' => ['nullable', Rule::in(Role::values())],
        ];
    }

    /**
     * Nicer names for the fields in the error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'role' => 'creator role',
        ];
    }
}
