<?php

namespace App\Http\Requests;

use App\Models\AssetManufacturer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetManufacturerRequest extends FormRequest
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
                "max:256",
                Rule::unique('asset_manufacturers', 'name')->ignore($this->route('asset_manufacturer')['id'])
            ]
        ];
    }
}
