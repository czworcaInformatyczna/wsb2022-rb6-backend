<?php

namespace App\Http\Controllers;

use App\Exports\AssetFileExport;
use App\Models\AssetFile;
use App\Http\Requests\StoreAssetFileRequest;
use App\Http\Requests\UpdateAssetFileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssetFileController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:Manage Assets')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(AssetFile::class, 'asset_file');
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
            'search' => 'string|nullable|min:3|max:30',
            'sort' => [
                'string',
                Rule::in(['id', 'created_at', 'updated_at', 'name', 'extension', 'size'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'asset_id' => 'required|integer|exists:assets,id',
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $model = AssetFile::query();

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $model = $model->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%")
                    ->orWhere('extension', 'like', "%{$validated['search']}%")
                    ->orWhere('notes', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('uploader', 'name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('uploader', 'surname', 'like', "%{$validated['search']}%");
            });
        }

        if ($validated['asset_id'] ?? false) {
            $model = $model->where('asset_id', $validated['asset_id']);
        }

        if (($validated['sort'] ?? null) !== null) {
            $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new AssetFileExport($model))->download('asset_file.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAssetFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetFileRequest $request)
    {
        $validated = $request->validated();
        /**
         * @var \Illuminate\Http\UploadedFile
         */
        $file = $validated['file'];

        DB::beginTransaction();

        $assetFile = new AssetFile();
        try {
            $assetFile->uploader_id = Auth::user()->id;
            $assetFile->name = $file->getClientOriginalName();
            $assetFile->extension = $file->getClientOriginalExtension();
            $assetFile->size = $file->getSize();
            $assetFile->asset_id = $validated['asset_id'];
            $assetFile->save();

            Storage::delete('asset_files\\' . $assetFile->id . '.' . $assetFile->extension);

            Storage::putFileAs(
                'asset_files',
                $validated['file'],
                $assetFile->id . '.' . $assetFile->extension
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => $e
            ], 400);
        }

        return response()->json([
            "result" => true,
            "model" => AssetFile::where('id', $assetFile->id)->first()
        ]);
    }

    /**
     * Download specified file
     *
     * @param  \App\Models\AssetFile  $assetFile
     * @return \Illuminate\Http\Response
     */
    public function download(AssetFile $assetFile)
    {
        $this->authorize('download', $assetFile);

        return Storage::download('asset_files/' . $assetFile->id . '.' . $assetFile->extension);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssetFile  $assetFile
     * @return \Illuminate\Http\Response
     */
    public function show(AssetFile $assetFile)
    {
        return $assetFile;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssetFileRequest  $request
     * @param  \App\Models\AssetFile  $assetFile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssetFileRequest $request, AssetFile $assetFile)
    {
        $assetFile->fill($request->validated());
        $assetFile->save();
        return response()->json([
            "result" => $assetFile->save(),
            "assetModel" => $assetFile
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssetFile  $assetFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetFile $assetFile)
    {
        $result = $assetFile->delete();
        if ($result) {
            Storage::delete('asset_files\\' . $assetFile->id . '.' . $assetFile->extension);
        }
        return response()->json([
            "result" => $result
        ]);
    }
}
