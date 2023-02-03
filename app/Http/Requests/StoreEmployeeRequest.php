<?php

namespace App\Http\Requests;


class StoreEmployeeRequest extends StatelessRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' 		=> 'required|unique:employees|max:255',
            'name' 		=> 'required|max:255',
            'employee_id' 	=> 'required|regex:/^[0-9]{2}[-][A-Z]{1,}$/'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' 	=> 'You must enter an email',
            'email.unique' 	=> 'That email is already taken, please try again or reset your password',
            'email.max' 	=> 'Talk to the boss man, i wanted 256, he said no.', 
            
            'name.required' 	=> 'You Must Enter An Name',
            'name.max' 		=> 'Okay maybe its actually a database limitation, but still', 

            'employee_id' 	=> 'employee_id should be in format of 11-ABCDEFG'
        ];
    }
}

