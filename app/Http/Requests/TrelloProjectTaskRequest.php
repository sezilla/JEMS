<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrelloProjectTaskRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trello_board_data' => 'required|array',
            'trello_board_data.*' => 'array',
            'trello_board_data.*.*' => 'array',
        ];
    }
}
