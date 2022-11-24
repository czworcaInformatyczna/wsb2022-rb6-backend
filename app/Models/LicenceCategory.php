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

    public function licence(){
        return $this->belongsTo(Licence::class, 'category_id', 'id');
    }
}
