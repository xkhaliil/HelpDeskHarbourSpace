<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateTicketRequest extends FormRequest
{
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
            'status' => ['required', Rule::in(TicketStatus::values())],
            'priority' => ['required', Rule::in(TicketPriority::values())],
            // The ids of the admins who handle the ticket (the checkboxes). Optional: nobody may be chosen.
            'assignees' => ['nullable', 'array'],
            // Every chosen id must be a user with the admin role.
            'assignees.*' => ['integer', Rule::exists('users', 'id')->where('role', Role::Admin->value)],
        ];
    }

    /**
     * Custom error messages, shown instead of the standard Laravel messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'assignees.*.exists' => 'Only members of the IT staff (admins) can handle a ticket.',
        ];
    }
}
