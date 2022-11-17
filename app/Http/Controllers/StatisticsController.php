<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetModel;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $assetStatusStats = Asset::select('status', DB::raw('count(*) as count'))
            ->withOnly([])
            ->orderBy('status')
            ->groupBy('status')
            ->get();

        $assetCategoryStats = AssetCategory::select('id', 'name')
            ->withOnly([])
            ->withCount(['models', 'assets'])
            ->having('models_count', '>', 0)
            ->having('assets_count', '>', 0)
            ->get();

        $assetModelStats = AssetModel::select('id', 'name')
            ->withOnly([])
            ->withCount(['assets'])
            ->having('assets_count', '>', 0)
            ->get();

        $manufacturerStats = Manufacturer::select('id', 'name')
            ->withOnly([])
            ->withCount(['assets'])
            ->having('assets_count', '>', 0)
            ->get();

        $topUsersWithAssets = User::select(['id', 'name'])
            ->withOnly([])
            ->withCount(['assets'])
            ->orderByDesc('assets_count')
            ->having('assets_count', '>', 0)
            ->limit(10)
            ->get();

        return [
            'assetCategory' => $assetCategoryStats,
            'assetModel' => $assetModelStats,
            'assetStatus' => $assetStatusStats,
            'manufacturer' => $manufacturerStats,
            'user' => $topUsersWithAssets
        ];
    }
}
