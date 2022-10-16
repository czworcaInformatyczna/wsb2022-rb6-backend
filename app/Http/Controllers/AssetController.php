<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
    public function index()
    {
        return Asset::all();
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
