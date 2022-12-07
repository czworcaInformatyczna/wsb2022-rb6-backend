<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\AssetComponentCategory;
use App\Http\Requests\StoreAssetComponentCategoryRequest;
use App\Http\Requests\UpdateAssetComponentCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class AssetComponentCategoryController extends Controller
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
                Rule::in(['id', 'created_at', 'updated_at', 'name'])
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
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $model = AssetComponentCategory::query();

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $model = $model->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new GenericExport($model))->download('asset_component_model.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetComponentCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetComponentCategoryRequest $request)
    {
        $assetComponentCategory = new AssetComponentCategory($request->validated());
        $saved = $assetComponentCategory->save();
        return response()->json([
            "result" => $saved,
            "model" => $saved ? $assetComponentCategory : null,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetComponentCategory  $assetComponentCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AssetComponentCategory $assetComponentCategory)
    {
        return $assetComponentCategory;
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
        $assetComponentCategory->fill($request->validated());
        $assetComponentCategory->save();
        return response()->json([
            "result" => "success",
            "model" => $assetComponentCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetComponentCategory  $assetComponentCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetComponentCategory $assetComponentCategory)
    {
        return response()->json([
            "result" => $assetComponentCategory->delete()
        ]);
    }
}
