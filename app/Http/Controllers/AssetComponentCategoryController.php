<?php

namespace App\Http\Controllers;

use App\Models\AssetComponentCategory;
use App\Http\Requests\StoreAssetComponentCategoryRequest;
use App\Http\Requests\UpdateAssetComponentCategoryRequest;

class AssetComponentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetComponentCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetComponentCategoryRequest $request)
    {
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetComponentCategory  $assetComponentCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AssetComponentCategory $assetComponentCategory)
    {
        return true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetComponentCategoryRequest  $request
     * @param  \App\Models\AssetComponentCategory  $assetComponentCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetComponentCategoryRequest $request, AssetComponentCategory $assetComponentCategory)
    {
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetComponentCategory  $assetComponentCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetComponentCategory $assetComponentCategory)
    {
        return true;
    }
}
