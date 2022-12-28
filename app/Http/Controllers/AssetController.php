<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\LogActionType;
use App\Enums\LogItemType;
use App\Http\Requests\StoreAssetImageRequest;
use App\Exports\AssetExport;
use App\Models\Asset;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:Manage Assets')->only(['store', 'update', 'destroy', 'storeImage', 'destroyImage', 'parseImage']);
        $this->authorizeResource(Asset::class, 'asset');
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
                Rule::in(['id', 'name', 'notes', 'order_number', 'price', 'purchase_date', 'serial', 'status', 'tag', 'warranty', 'updated_at'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'status' => [
                'integer',
                new Enum(AssetStatus::class)
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $asset = Asset::query();

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $asset = $asset->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%")
                    ->orWhere('tag', 'like', "%{$validated['search']}%")
                    ->orWhere('serial', 'like', "%{$validated['search']}%")
                    ->orWhere('notes', 'like', "%{$validated['search']}%")
                    ->orWhere('order_number', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('asset_model', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('asset_model.category', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('asset_model.manufacturer', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('current_holder', 'name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['status'] ?? null) !== null) {
            $asset = $asset->where('status', $validated['status']);
        }

        if (($validated['sort'] ?? null) !== null) {
            $asset = $asset->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new AssetExport($asset))->download('assets.xlsx');
        }

        return $asset->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetRequest $request)
    {

        $validated = $request->validated();

        // Create model
        $asset = new Asset($validated);

        if ($asset->must_have_holder() && !$asset->current_holder_id) {
            return response()->json([
                "message" => "Asset must have a holder with current status"
            ], 400);
        }

        if ($asset->can_have_holder() == false && $asset->current_holder_id) {
            $asset->current_holder_id = null;
        }


        $dirty = $asset->getDirty(); // For logs


        try {
            DB::beginTransaction();

            if (($validated['image'] ?? null) != null) {
                // Save to get $asset->id
                $asset->save();
                $asset->has_image = true;
                $asset->image_extension = $this->parseImage($asset, $validated['image']);
            }

            $asset->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "message" => "Error occured during image upload"
            ], 400);
        }

        DB::commit();

        Log::newEntry(
            LogActionType::Created,
            $dirty,
            LogItemType::Asset,
            $asset->id
        );

        return response()->json([
            "result" => true,
            "model" => $asset
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function show(Asset $asset)
    {
        return $asset;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetRequest  $request
     * @param  \App\Models\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $validated = $request->validated();

        $asset->fill($validated);

        if ($asset->must_have_holder() && !$asset->current_holder_id) {
            return response()->json([
                "message" => "Asset must have a holder with current status"
            ], 400);
        }

        if ($asset->can_have_holder() == false && $asset->current_holder_id) {
            $asset->current_holder_id = null;
        }

        $dirty = $asset->getDirty(); // For logs

        $asset->save();

        Log::newEntry(
            LogActionType::Updated,
            $dirty,
            LogItemType::Asset,
            $asset->id
        );

        return response()->json([
            "result" => "success",
            "model" => $asset
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Asset $asset)
    {
        return response()->json([
            "result" => $asset->delete()
        ]);
    }

    /**
     * Downloads image of an asset
     *
     * @param Asset $asset
     * @return \Illuminate\Http\Response
     */
    public function downloadImage(Asset $asset, string $extension = null)
    {
        if ($asset->has_image == null) {
            return response()->json([
                "message" => "This asset has no image"
            ], 400);
        }

        if ("." . $asset->image_extension !== $extension) {
            return response()->json([
                "message" => "Invalid extension suffix"
            ], 400);
        }

        return Storage::response('asset_image/' . $asset->id . '.' . $asset->image_extension);
    }

    /**
     * Insert new or replace existing file in an asset
     *
     * @param StoreAssetImageRequest $request
     * @param Asset $asset
     * @return \Illuminate\Http\Response
     */
    public function storeImage(StoreAssetImageRequest $request, Asset $asset)
    {
        DB::beginTransaction();

        try {
            // Save to get $asset->id (used in filename)
            $asset->has_image = true;
            $asset->image_extension = $this->parseImage($asset, $request->validated()['image']);
            $asset->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "message" => "Error occured during image upload"
            ], 400);
        }

        DB::commit();

        return response()->json([
            "result" => "success",
            "model" => $asset
        ]);
    }

    /**
     * Removes an asset image if it exists
     *
     * @param Asset $asset
     * @return \Illuminate\Http\Response
     */
    public function destroyImage(Asset $asset)
    {
        $asset->has_image = false;
        $asset->image_extension = null;
        $asset->save();
        Storage::delete('asset_image\\' . $asset->id . '.' . $asset->image_extension);
        return response()->json([
            "result" => "success"
        ]);
    }

    /**
     * Returns $asset->id as a QR Code
     *
     * @param Asset $asset
     * @return \Illuminate\Http\Response
     */
    public function qr_code(Asset $asset)
    {
        return QrCode::errorCorrection('H')->size(300)->generate($asset->id);
    }

    /**
     * Saves given image and assigns it to an asset
     *
     * @param Asset $asset
     * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile $image
     * @return string Extension
     */
    public static function parseImage(Asset $asset, $image): string
    {
        $fileName = $asset->id . '.' . $image->getClientOriginalExtension();
        Storage::delete('asset_image\\' . $fileName);
        Storage::putFileAs('asset_image', $image, $fileName);
        return $image->getClientOriginalExtension();
    }
}
