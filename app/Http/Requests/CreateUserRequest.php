<?php

namespace App\Http\Requests;

class CreateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string',
            'furigana_name' => 'required|string',
            'email'         => 'required|email',
            'login_id'      => ['required', 'string', 'unique:users,login_id', 'regex:/^[a-zA-Z0-9]+$/u'],
            'password'      => ['required', 'string', 'min:8', 'confirmed', 'regex:/^.(?=.*[a-zA-Z0-9!$#%]).{7,}$/u'],
        ];
    }
}
