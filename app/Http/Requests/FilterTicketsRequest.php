<?php

namespace App\Http\Requests;

use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTicketsRequest extends FormRequest
{
    /**
     * Where to go when the filter is invalid (instead of the previous page).
     *
     * @var string
     */
    protected $redirectRoute = 'tickets.index';

    /**
     * Every logged-in user may filter their own tickets.
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
            'status' => ['nullable', Rule::in(TicketStatus::values())],
        ];
    }
}
