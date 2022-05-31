<?php

namespace App\Http\Requests;

use App\Enums\LanguageEnum;
use App\Enums\ModelTypeEnum;
use App\Models\ModelHasFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachFileToModelRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'file_ids'   => 'required|array|max:20',
            'file_ids.*' => 'required|exists:files,id',
        ];
    }

    public function withValidator($validator) {

        $modelId = $this->route('modelId');
        $modelType = $this->route('modelType');

        $modelFile = ModelHasFile::where('model_id', $modelId)
                                 ->where('model_type', $modelType)
                                 ->whereIn('file_id', $this->file_ids);

        $validator->after(function ($validator) use ($modelFile) {
            if ($modelFile->count() != 0) {
                $validator->errors()->add('message', 'It has one or more duplicate data');
            }
        });
    }
}
