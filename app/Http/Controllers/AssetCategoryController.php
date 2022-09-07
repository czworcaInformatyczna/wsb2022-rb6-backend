<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;

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
    public function index()
    {
        return AssetCategory::all();
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
        return $category->save();
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
        $assetCategory->save();
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
