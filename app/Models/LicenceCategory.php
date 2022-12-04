<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function licences()
    {
        return $this->hasMany(Licence::class, 'category_id', 'id');
    }
}
