<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetModelRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255|unique:asset_models,name',
            'asset_category_id' => 'required|numeric|integer|exists:asset_categories,id',
            'asset_manufacturer_id' => 'required|numeric|integer|exists:asset_manufacturers,id'
        ];
    }
}