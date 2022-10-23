<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'asset_model_id',
        'image',
        'serial',
        'status',
        'current_holder_id',
        'notes',
        'warranty',
        'purchase_date',
        'order_number',
        'price'
    ];

    protected $with = [
        'asset_model',
        'current_holder'
    ];

    protected $casts = [
        'status' => AssetStatus::class,
        'purchase_date' => 'datetime:Y-m-d'
    ];

    public function asset_model()
    {
        return $this->belongsTo(AssetModel::class, 'asset_model_id');
    }

    public function current_holder()
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    public function can_have_holder(): bool
    {
        return match($this->status) {
            AssetStatus::Archived, AssetStatus::Ready => false,
            AssetStatus::Serviced, AssetStatus::HandedOver => true
        };
    }
    public function must_have_holder(): bool
    {
        return match($this->status) {
            AssetStatus::Archived, AssetStatus::Ready, AssetStatus::Serviced => false,
            AssetStatus::HandedOver => true
        };
    }
}
