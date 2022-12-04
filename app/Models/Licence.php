<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'manufacturer_id',
        'category_id',
        'product_key',
        'email',
        'slots',
        'expiration_date',
        'reassignable'
    ];

    public function category()
    {
        return $this->belongsTo(LicenceCategory::class);
    }

    public function history()
    {
        return $this->hasOne(LicenceHistory::class, 'licence_id', 'id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'licencable');
    }

    public function assets()
    {
        return $this->morphedByMany(Asset::class, 'licencable');
    }
}
