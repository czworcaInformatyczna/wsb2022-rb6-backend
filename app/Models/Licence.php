<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    use HasFactory;
    protected $fillable = [
        'manufacturer_id',
        'category_id',
        'product_key',
        'email',
        'expiration_date',
        'reassignable'
    ];

    public function category()
    {
        return $this->hasOne(LicenceCategory::class, 'id', 'category_id');
    }

    public function history()
    {
        return $this->hasOne(LicenceHistory::class, 'licence_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
