<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'asset_model_id',
        'image',
        'serial',
        'status'
    ];

    protected $with = [
        'asset_model'
    ];

    public function asset_model()
    {
        return $this->belongsTo(AssetModel::class, 'asset_model_id');
    }
}
