<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetComponentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id'
            ],
            'asset_component_category_id' => [
                'required',
                'integer',
                'exists:asset_component_categories,id'
            ],
            'manufacturer_id' => [
                'nullable',
                'integer',
                'exists:manufacturers,id'
            ],
            'name' => [
                'required',
                'string',
                'max:128'
            ],
            'serial' => [
                'nullable',
                'string',
                'max:128'
            ]
        ];
    }
}
