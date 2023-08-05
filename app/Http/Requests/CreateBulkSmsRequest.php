<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBulkSmsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'csv_file' => 'required|mimes:csv,txt',
            'blast_name' => 'required|string',
            'from_number' => 'required|string',
            'is_schedule' => 'required|boolean',
            'schedule_date' => 'required_if:is_schedule,1',
            'timezone' => 'required_if:is_schedule,1',
            'message' => 'required|string'
        ];
    }
}
