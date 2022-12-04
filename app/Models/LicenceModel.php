<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceModel extends Model
{
    use HasFactory;
    private $fillable = [
        'assignable_id',
        'assignable_type'
    ];

    public function assignable()
    {
        return $this->morphTo();
    }
}
