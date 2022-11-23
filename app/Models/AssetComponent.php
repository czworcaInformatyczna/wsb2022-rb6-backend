<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Asset component
 * @property-read integer $id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property integer $asset_id
 * @property integer $asset_component_category_id
 * @property integer|null $manufacturer_id
 * @property string $name max:128
 * @property string|null $serial max:128
 * @property-read Asset $asset
 * @property-read Manufacturer $manufacturer
 * @property-read AssetComponentCategory $assetComponentCategory
 */
class AssetComponent extends Model
{
    use HasFactory;

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function assetComponentCategory()
    {
        return $this->belongsTo(AssetComponentCategory::class, 'asset_component_category_id');
    }
}
