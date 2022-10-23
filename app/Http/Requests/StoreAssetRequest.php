<?php

namespace App\Http\Requests;

use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAssetRequest extends FormRequest
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
            'name' => 'required|string|min:0|max:250',
            'tag' => 'required|string|min:1|max:30|unique:assets,tag',
            'asset_model_id' => 'required|integer|exists:asset_models,id',
            'image' => 'regex:/^data:image\/((png)|(jpg)|(jpeg));base64,(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/',
            'serial' => 'required|string|min:1|max:250',
            'status' => [
                'required',
                new Enum(AssetStatus::class)
            ],
            'current_holder_id' => 'integer|exists:users,id'
        ];
    }
}
