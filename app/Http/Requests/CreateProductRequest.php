<?php

namespace App\Http\Requests;

use App\Enums\TaxCalculateType;
use App\Enums\TaxType;
use Illuminate\Validation\Rule;

class CreateProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'                           => 'required',
            'description'                    => 'required|max:1000',
            'public_duration_start'          => 'required|date',
            'public_duration_end'            => 'required|date',
            'unit_price'                     => 'required|min:0',
            'tax_classification'             => ['required', Rule::in(TaxType::asArray())],
            'tax_rate'                       => 'required|between:0,100',
            'tax_calculation_classification' => ['required', Rule::in(TaxCalculateType::asArray())],
            'stock_quantity'                 => 'required|integer|min:0',
            'min_order'                      => ['required', 'integer', 'min:0', 'max:'.$this->stock_quantity],
            'max_order'                      => [
                'required',
                'integer',
                'gte:'.$this->min_order,
                'max:'.$this->stock_quantity,
            ],
            'header_image'                   => 'required|image|mimes:jpg,jpeg,jpe,png,gif|max:5120',
            'detail_image'                   => 'required|image|mimes:jpg,jpeg,jpe,png,gif|max:5120',
        ];
    }
}
