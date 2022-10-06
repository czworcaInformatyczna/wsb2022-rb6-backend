<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    public $timestamps = false;
    public function cookies(){
        return $this->belongsToMany(Cookie::class);
    }
}