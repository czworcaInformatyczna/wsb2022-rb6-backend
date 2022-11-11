<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function assets()
    {
        return $this->hasManyThrough(Asset::class, AssetModel::class);
    }
}
