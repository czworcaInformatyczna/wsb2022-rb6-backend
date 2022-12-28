<?php

namespace App\Http\Controllers;

use App\Exports\LicenceFileExport;
use Illuminate\Http\Request;
use App\Models\LicenceFile;
use App\Models\LicenceHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LicenceFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Manage Licences')->only(['store', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $licenceId)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'search' => 'string|nullable|min:3|max:30',
            'export' => 'boolean'
        ]);

        $licenceFile = LicenceFile::query();
        $licenceFile->where('licence_id', $licenceId)
            ->with('uploader')
            ->with('licence');

        if ($request->export) {
            return (new LicenceFileExport($licenceFile))->download('licenceFiles.xlsx');
        }

        return $licenceFile->paginate($validated['per_page'] ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $licenceId)
    {
        $validated = $request->validate([
            'file' => 'required|file',
            'notes' => 'string|max:256'
        ]);
        $file = $validated['file'];

        DB::beginTransaction();

        $licenceFile = new LicenceFile();
        try {
            $licenceFile->uploader_id = Auth::user()->id;
            $licenceFile->name = $file->getClientOriginalName();
            $licenceFile->extension = $file->getClientOriginalExtension();
            $licenceFile->size = $file->getSize();
            $licenceFile->licence_id = $licenceId;
            $licenceFile->notes = $request->notes;
            $licenceFile->save();

            Storage::delete('licence_files\\' . $licenceFile->id . '.' . $licenceFile->extension);

            Storage::putFileAs(
                'licence_files',
                $validated['file'],
                $licenceFile->id . '.' . $licenceFile->extension
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return 'coś się popsuło';
            return response()->json([
                "message" => $e
            ], 400);
        }

        LicenceHistory::create([
            'user_id' => auth()->user()->id,
            'licence_id' => $licenceId,
            'action' => 'create',
            'model' => 'LicenceFile',
            'model_id' => $licenceFile->id
        ]);

        return response()->json([
            "result" => true,
            "model" => LicenceFile::where('id', $licenceFile->id)->first()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($licenceId, $id)
    {
        return LicenceFile::where('licence_id', $licenceId)
            ->with('licence')
            ->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $licenceId, $id)
    {
        $request->validate([
            'name' => 'string|min:2|max:32',
            'notes' => 'string|max:256'
        ]);
        $licenceFile = LicenceFile::where('licence_id', $licenceId)
            ->find($id);
        if ($licenceFile) {
            $licenceFile->update([
                'name' => $request->name,
                'notes' => $request->notes
            ]);
            $licenceFile->save();
            return response()->json([
                "licenceModel" => $licenceFile
            ]);
        }

        LicenceHistory::create([
            'user_id' => auth()->user()->id,
            'licence_id' => $licenceId,
            'action' => 'edit',
            'model' => 'LicenceFile',
            'model_id' => $licenceFile->id
        ]);

        return response()->json([
            'message' => 'There is no file with id ' . $id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($licenceId, $id)
    {
        $licenceFile = LicenceFile::find($id);
        if ($licenceFile) {
            Storage::delete('licence_files\\' . $licenceFile->id . '.' . $licenceFile->extension);
            $licenceFile->delete();
            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licenceFile->licence->id,
                'action' => 'delete',
                'model' => 'LicenceFile',
                'model_id' => $id
            ]);
            return response()->json([
                "message" => 'File has been deleted'
            ]);
        }
        return 'There is no file with id ' . $id;
    }

    public function download($licenceId, $id)
    {
        $licenceFile = LicenceFile::find($id);
        if ($licenceFile) {
            return response()->download(storage_path('app/licence_files/' . $licenceFile->id . '.' . $licenceFile->extension), $licenceFile->name . '.' . $licenceFile->extenion);
        }
        return response()->json([
            'message' => 'There is no file with id ' . $id
        ]);
    }
}
