<?php

namespace App\Http\Controllers;

use App\Models\AssetModel;
use App\Http\Requests\StoreAssetModelRequest;
use App\Http\Requests\UpdateAssetModelRequest;

class AssetModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AssetModel::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetModelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetModelRequest $request)
    {
        $model = new AssetModel($request->validated());
        $saved = $model->save();

        return response()->json([
            "result" => $saved,
            /* AssetModel below is get from database not from $model variable because
               $model doesn't contain $with relationships */
            "model" => $saved ? AssetModel::where('id', $model->id)->get() : null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetModel  $assetModel
     * @return \Illuminate\Http\Response
     */
    public function show(AssetModel $assetModel)
    {
        return $assetModel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetModelRequest  $request
     * @param  \App\Models\AssetModel  $assetModel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetModelRequest $request, AssetModel $assetModel)
    {
        $assetModel->fill($request->validated());
        $assetModel->save();
        return response()->json([
            "result" => "success",
            "assetModel" => $assetModel
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetModel  $assetModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetModel $assetModel)
    {
        return response()->json([
            "result" => $assetModel->delete()
        ]);
    }
}
