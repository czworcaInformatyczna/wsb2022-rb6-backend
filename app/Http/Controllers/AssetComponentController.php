<?php

namespace App\Http\Controllers;

use App\Exports\AssetComponentExport;
use App\Models\AssetComponent;
use App\Http\Requests\StoreAssetComponentRequest;
use App\Http\Requests\UpdateAssetComponentRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetComponentController extends Controller
{
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
                Rule::in(['id', 'created_at', 'updated_at', 'name', 'serial'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'search' => [
                'string',
                'nullable',
                'max:64',
                'min:1'
            ],
            'asset_id' => [
                'integer',
                'exists:assets,id'
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $model = AssetComponent::with(['assetComponentCategory', 'manufacturer'])
            ->withCount(['asset']);

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $model = $model->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%")
                    ->Where('serial', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('assetComponentCategory', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('manufacturer', 'name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if ($validated['asset_id'] ?? false) {
            $model = $model->where('asset_id', $validated['asset_id']);
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new AssetComponentExport($model))->download('asset_component.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetComponentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetComponentRequest $request)
    {
        $assetComponent = new AssetComponent($request->validated());
        $saved = $assetComponent->save();
        return response()->json([
            "result" => $saved,
            "model" => $saved ? $assetComponent : null,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetComponent  $assetComponent
     * @return \Illuminate\Http\Response
     */
    public function show(AssetComponent $assetComponent)
    {
        return $assetComponent;
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
        $assetComponent->fill($request->validated());
        $result = $assetComponent->save();
        return response()->json([
            "result" => $result,
            "model" => $assetComponent
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetComponent  $assetComponent
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetComponent $assetComponent)
    {
        return response()->json([
            "result" => $assetComponent->delete()
        ]);
    }
}
