<?php

namespace App\Http\Controllers;

use App\Models\AssetManufacturer;
use App\Http\Requests\StoreAssetManufacturerRequest;
use App\Http\Requests\UpdateAssetManufacturerRequest;

class AssetManufacturerController extends Controller
{
        /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(AssetManufacturer::class, 'asset_manufacturer');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AssetManufacturer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetManufacturerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetManufacturerRequest $request)
    {
        $manufacturer = new AssetManufacturer($request->validated());
        return response()->json([
            "result" => $manufacturer->save()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetManufacturer  $assetManufacturer
     * @return \Illuminate\Http\Response
     */
    public function show(AssetManufacturer $assetManufacturer)
    {
        return $assetManufacturer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetManufacturerRequest  $request
     * @param  \App\Models\AssetManufacturer  $assetManufacturer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetManufacturerRequest $request, AssetManufacturer $assetManufacturer)
    {
        $assetManufacturer->fill($request->validated());
        $assetManufacturer->save();
        return response()->json([
            "result" => "success",
            "assetManufacturer" => $assetManufacturer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetManufacturer  $assetManufacturer
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetManufacturer $assetManufacturer)
    {
        return response()->json([
            "result" => $assetManufacturer->delete()
        ]);
    }
}
