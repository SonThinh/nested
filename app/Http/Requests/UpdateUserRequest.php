<?php

namespace App\Http\Requests;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string',
            'furigana_name' => 'sometimes|string',
            'email'         => 'sometimes|email',
            'login_id'      => ['sometimes', 'string', 'unique:users,login_id', 'regex:/^[a-zA-Z0-9]+$/u'],
            'password'      => ['sometimes', 'string', 'min:8', 'confirmed', 'regex:/^.(?=.*[a-zA-Z0-9!$#%]).{7,}$/u'],
        ];
    }
}
