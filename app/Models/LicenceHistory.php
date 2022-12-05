<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'licence_id',
        'user_id',
        'action',
        'target',
        'model',
        'model_id',
    ];

    public function licence()
    {
        return $this->hasOne(Licence::class, 'id', 'licence_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
