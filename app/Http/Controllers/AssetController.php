<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        if (array_key_exists('image', $validated)) {
            // Separate image from the rest of the array
            $image = $validated['image'];

            $validated['image'] = $this->parse_image($image);
        }

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

        $saved = $asset->save();

        return response()->json([
            "result" => $saved,
            "model" => $saved ? $asset : null
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
        if (array_key_exists('image', $validated) && !$validated['image']) {
            unset($validated['image']);
            $asset->image = null;
        } else if (array_key_exists('image', $validated)) {
            // Separate image from the rest of the array
            $validated['image'] = $this->parse_image($validated['image']);
        }
        $asset->fill($validated);

        if ($asset->must_have_holder() && !$asset->current_holder_id) {
            return response()->json([
                "message" => "Asset must have a holder with current status"
            ], 400);
        }

        if ($asset->can_have_holder() == false && $asset->current_holder_id) {
            $asset->current_holder_id = null;
        }

        $asset->save();
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
        return $asset->delete();
    }

    public function qr_code(Asset $asset)
    {
        return QrCode::errorCorrection('H')->size(300)->generate($asset->tag);
    }

    public static function parse_image($image): string
    {
        // Process image
        $imageName = Carbon::now()->timestamp . "-" . Str::random(15);
        $imageFormat = Str::substr(Str::lower(Str::before($image, ';')), 11);

        $image = str_replace(Str::before($image, '64,') . '64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageFullName = $imageName . '.' . $imageFormat;

        Storage::disk('public')->put($imageFullName, base64_decode($image));

        return $imageFullName;
    }
}
