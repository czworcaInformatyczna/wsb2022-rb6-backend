<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetModelRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            "name" => [
                "string",
                "min:3",
                "max:255",
                Rule::unique('asset_models', 'name')->ignore($this->route('asset_model')['id'])
            ],
            'asset_category_id' => 'numeric|integer|exists:asset_categories,id',
            'manufacturer_id' => 'numeric|integer|exists:manufacturers,id'
        ];
    }
}
