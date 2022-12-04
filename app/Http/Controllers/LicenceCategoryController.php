<?php

namespace App\Http\Controllers;

use App\Models\LicenceCategory;
use App\Models\LicenceHistory;
use Illuminate\Http\Request;

class LicenceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return LicenceCategory::select('id', 'name')
            ->paginate(25);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|required|min:2|max:32'
        ]);

        LicenceCategory::create([
            'name' => $validated['name']
        ]);

        return response()->json([
            'message' => 'Licence category has been created'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return LicenceCategory::with('licences')
            ->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|required|min:2|max:32'
        ]);

        $licenceCategory = LicenceCategory::find($id);

        if ($licenceCategory) {
            $licenceCategory->update(['name' => $validated['name']]);

            return response()->json([
                'message' => 'Licence category with id ' . $id . ' has been updated'
            ], 200);
        }

        return response()->json([
            'message' => 'There is no licence category with id ' . $id
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $licenceCategory = LicenceCategory::find($id);
        if ($licenceCategory) {
            $licenceCategory->delete();
            return response()->json([
                'message' => 'Licence category with id ' . $id . ' has been deleted'
            ], 200);
        }
        return response()->json([
            'message' => 'There is no licence category with id ' . $id
        ], 400);
    }
}
