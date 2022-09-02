<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cookie extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'theme_id',
        'language_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function theme(){
        return $this->hasOne(Theme::class, 'id', 'theme_id');
    }
    public function language(){
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
