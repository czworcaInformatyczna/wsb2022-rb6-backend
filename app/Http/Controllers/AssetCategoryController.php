<?php

namespace App\Http\Controllers;

use App\Enums\LogActionType;
use App\Enums\LogItemType;
use App\Exports\GenericExport;
use App\Models\AssetCategory;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetCategoryController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(AssetCategory::class, 'asset_category');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $assetCategory = AssetCategory::query();

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new GenericExport($assetCategory))->download('asset_category.xlsx');
        }
        return $assetCategory->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetCategoryRequest $request)
    {
        $category = new AssetCategory($request->validated());

        $dirty = $category->getDirty();

        $saved = $category->save();

        Log::newEntry(
            LogActionType::Created,
            $dirty,
            LogItemType::AssetCategory,
            $category->id
        );

        return response()->json([
            "result" => $saved,
            "model" => $saved ? $category : null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AssetCategory $assetCategory)
    {
        return $assetCategory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetCategoryRequest  $request
     * @param  \App\Models\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetCategoryRequest $request, AssetCategory $assetCategory)
    {
        $assetCategory->fill($request->validated());

        $dirty = $assetCategory->getDirty();

        $assetCategory->save();

        Log::newEntry(
            LogActionType::Updated,
            $dirty,
            LogItemType::AssetCategory,
            $assetCategory->id
        );

        return response()->json([
            "result" => "success",
            "assetCategory" => $assetCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetCategory $assetCategory)
    {
        return $assetCategory->delete();
    }
}
