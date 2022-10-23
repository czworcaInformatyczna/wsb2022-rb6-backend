<?php

namespace App\Http\Requests;

use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAssetRequest extends FormRequest
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
            'name' => 'string|min:1|max:250',
            'tag' => [
                'string',
                'min:1',
                'max:30',
                Rule::unique('asset_models', 'name')->ignore($this->route('asset')['id'])
            ],
            'asset_model_id' => 'integer|exists:asset_models,id',
            'image' => [
                'regex:/^data:image\/((png)|(jpg));base64,(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/',
                'nullable'
            ],
            'serial' => 'string|min:1|max:250',
            'status' => [
                new Enum(AssetStatus::class)
            ],
            'current_holder_id' => 'integer|nullable|exists:users,id'
        ];
    }
}
