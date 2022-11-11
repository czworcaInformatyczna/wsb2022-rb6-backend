<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function models()
    {
        return $this->hasMany(AssetModel::class);
    }

    public function assets()
    {
        return $this->hasManyThrough(Asset::class, AssetModel::class);
    }
}
