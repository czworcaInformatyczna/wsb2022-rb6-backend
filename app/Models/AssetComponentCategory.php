<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Asset component category
 *
 * @property-read integer $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property string $name
 */
class AssetComponentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
