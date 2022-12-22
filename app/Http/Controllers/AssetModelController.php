<?php

namespace App\Http\Controllers;

use App\Exports\AssetModelExport;
use App\Models\AssetModel;
use App\Http\Requests\StoreAssetModelRequest;
use App\Http\Requests\UpdateAssetModelRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetModelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Show Assets')->only(['index', 'show']);
        $this->middleware('permission:Manage Assets')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'search' => 'string|nullable|min:1|max:30',
            'sort' => [
                'string',
                Rule::in(['id', 'created_at', 'updated_at', 'name'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $model = AssetModel::query();

        if ($validated['search'] ?? false) {
            $model = $model->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('category', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('manufacturer', 'name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new AssetModelExport($model))->download('asset_model.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
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
