<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'asset_category_id',
        'asset_manufacturer_id'
    ];

    protected $with = [
        'category',
        'manufacturer'
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(AssetManufacturer::class, 'asset_manufacturer_id');
    }
}
