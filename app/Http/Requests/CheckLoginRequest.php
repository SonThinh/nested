<?php

namespace App\Http\Requests;

class CheckLoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules(): array
    {
        return [
            'login_id' => 'required',
            'password' => 'required',
        ];
    }
}
