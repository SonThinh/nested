<?php

namespace App\Http\Requests;

use App\Enums\ModelTypeEnum;
use Illuminate\Validation\Rule;

class UploadFileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type'        => ['nullable', Rule::in(ModelTypeEnum::asArray())],
            'is_active'   => 'required|boolean',
            'files'       => 'required|array|max:20',
            'files.*'     => 'required|mimes:jpg,png,jpeg,doc,docx,pdf,csv,ico,svg,xlsx,xls|max:5120',
        ];
    }
}
