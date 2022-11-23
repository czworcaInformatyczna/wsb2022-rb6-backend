<?php

namespace App\Http\Controllers;

use App\Models\AssetComponent;
use App\Http\Requests\StoreAssetComponentRequest;
use App\Http\Requests\UpdateAssetComponentRequest;

class AssetComponentController extends Controller
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
     * @param  \App\Http\Requests\StoreAssetComponentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetComponentRequest $request)
    {
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetComponent  $assetComponent
     * @return \Illuminate\Http\Response
     */
    public function show(AssetComponent $assetComponent)
    {
        return true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetComponentRequest  $request
     * @param  \App\Models\AssetComponent  $assetComponent
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetComponentRequest $request, AssetComponent $assetComponent)
    {
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetComponent  $assetComponent
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetComponent $assetComponent)
    {
        return true;
    }
}
