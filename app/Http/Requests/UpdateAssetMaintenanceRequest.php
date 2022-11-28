<?php

namespace App\Http\Requests;

use App\Enums\AssetMaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateAssetMaintenanceRequest extends FormRequest
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
            'title' => 'string|min:1|max:300',
            'maintenance_type' => [
                new Enum(AssetMaintenanceType::class)
            ],
            'start_date' => 'date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_id' => 'integer|exists:users,id',
            'notes' => 'nullable|string'
        ];
    }
}
