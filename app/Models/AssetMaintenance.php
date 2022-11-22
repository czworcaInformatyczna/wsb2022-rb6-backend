<?php

namespace App\Models;

use App\Enums\AssetMaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Asset maintenance class
 *
 * @property-read integer $id
 * @property-read Carbon $created_at
 * @property Carbon $updated_at
 * @property string $title
 * @property AssetMaintenanceType $maintenance_type
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property integer $user_id
 * @property string $notes
 * @property-read integer $asset_id
 *
 * @property-read User $user
 * @property-read Asset $asset
 */
class AssetMaintenance extends Model
{
    use HasFactory;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'maintenance_type' => AssetMaintenanceType::class
    ];

    protected $fillable = [
        'asset_id',
        'title',
        'maintenance_type',
        'start_date',
        'end_date',
        'user_id',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
