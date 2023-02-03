<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends StatelessRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'email' => 'max:255',
            'name' => 'max:255',
            'employee_id' => 'regex:/^[0-9]{2}[-][A-Z]{1,}$/'
        ];
    }
}

