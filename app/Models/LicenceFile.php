<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function licence()
    {
        return $this->belongsTo(Licence::class, 'licence_id');
    }
}
