<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class StatelessRequest extends FormRequest {

    /**
     * We will handle this a different way
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * If  you look in \Illuminate\Foundation\Http\FormRequest@failedValidation, heres what you see
     *  
     * throw (new ValidationException($validator))
     *              ->errorBag($this->errorBag)
     *               ->redirectTo($this->getRedirectUrl());
     * 
     * I mean, its called an "HttpFormRequest" object, so it's probably not fair to call it a "mistake"
     * But either way, the hardcoded redirect method is assuming client state, and we dont do that here.  
     * 
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    abstract public function rules();

}
