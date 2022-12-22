<?php

namespace App\Http\Controllers;

use App\Enums\AssetMaintenanceType;
use App\Exports\AssetMaintenanceExport;
use App\Models\AssetMaintenance;
use App\Http\Requests\StoreAssetMaintenanceRequest;
use App\Http\Requests\UpdateAssetMaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class AssetMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Show Assets')->only(['index', 'show']);
        $this->middleware('permission:Manage Assets')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'search' => 'string|nullable|min:1|max:30',
            'sort' => [
                'string',
                Rule::in(['id', 'created_at', 'updated_at', 'title', 'maintenance_type', 'user_id', 'notes'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'maintenance_type' => [
                'string',
                new Enum(AssetMaintenanceType::class)
            ],
            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id'
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);
        $model = AssetMaintenance::with(['user']);

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $model = $model->where(function ($query) use ($validated) {
                $query->Where('title', 'like', "%{$validated['search']}%")
                    ->orWhere('notes', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if ($validated['maintenance_type'] ?? false) {
            $model = $model->where('maintenance_type', $validated['maintenance_type']);
        }

        if ($validated['asset_id'] ?? false) {
            $model = $model->where('asset_id', $validated['asset_id']);
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new AssetMaintenanceExport($model->with('asset')))->download('asset_maintenance.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetMaintenanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetMaintenanceRequest $request)
    {
        $assetMaintenance = new AssetMaintenance($request->validated());
        $saved = $assetMaintenance->save();
        return response()->json([
            "result" => $saved,
            "model" => $saved ? $assetMaintenance : null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetMaintenance  $assetMaintenance
     * @return \Illuminate\Http\Response
     */
    public function show(AssetMaintenance $assetMaintenance)
    {
        return $assetMaintenance->load(['user', 'asset']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetMaintenanceRequest  $request
     * @param  \App\Models\AssetMaintenance  $assetMaintenance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetMaintenanceRequest $request, AssetMaintenance $assetMaintenance)
    {
        $assetMaintenance->fill($request->validated());
        $assetMaintenance->save();
        return response()->json([
            "result" => "success",
            "model" => $assetMaintenance
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetMaintenance  $assetMaintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetMaintenance $assetMaintenance)
    {
        return response()->json([
            "result" => $assetMaintenance->delete()
        ]);
    }
}
