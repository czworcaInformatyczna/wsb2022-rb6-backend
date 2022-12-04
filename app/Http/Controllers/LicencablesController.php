<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Licence;
use App\Models\User;
use App\Models\LicenceHistory;
use Illuminate\Http\Request;

class LicencablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($licenceId)
    {
        $licence = Licence::with('users')->find($licenceId);
        $json = json_decode('{ "users":' . $licence->users . ', "assets":' . $licence->assets . ' }');
        return $json;
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
            'model' => 'string|required',
            'model_id' => 'integer|required'
        ]);
        $licence = Licence::find($licenceId);
        if (!$licence) {
            return response()->json([
                'message' => 'There is no licence with id ' . $licenceId
            ], 400);
        }
        $sum = $licence->users()->count() + $licence->assets()->count();
        if ($licence->slots < $sum) {
            return response()->json([
                'message' => 'Exceeded maximum licence slots'
            ], 400);
        }
        if ($validated['model'] == 'users') {
            if (!User::find($validated['model_id'])) {
                return response()->json([
                    'message' => 'There is no user with id ' . $validated['model_id']
                ], 400);
            }
            foreach ($licence->users as $user) {
                if ($user->id == $validated['model_id']) {
                    return response()->json([
                        'message' => 'This user has already attached this licence'
                    ]);
                }
            }
            $licence->users()->attach($validated['model_id']);
            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licenceId,
                'action' => 'assign',
                'model' => $validated['model'],
                'model_id' => $validated['model_id']
            ]);
            return response()->json([
                'message' => 'Success'
            ], 200);
        } else if ($validated['model'] == 'assets') {
            if (!Asset::find($validated['model_id'])) {
                return response()->json([
                    'message' => 'There is no asset with id ' . $validated['model_id']
                ], 400);
            }
            foreach ($licence->assets as $asset) {
                if ($asset->id == $validated['model_id']) {
                    return response()->json([
                        'message' => 'This asset has already attached this licence'
                    ]);
                }
            }
            $licence->assets()->attach($validated['model_id']);
            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licenceId,
                'action' => 'assign',
                'model' => $validated['model'],
                'model_id' => $validated['model_id']
            ]);
            return response()->json([
                'message' => 'Success'
            ], 200);
        }
        return response()->json([
            'message' => 'Unnkown model ' . $validated['model']
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($licenceId, $id)
    {
        //
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
        $validated = $request->validate([
            'model' => 'string|required',
            'model_id' => 'integer|required'
        ]);
        $licence = Licence::find($licenceId);
        if (!$licence) {
            return response()->json([
                'message' => 'There is no licence with id ' . $licenceId
            ], 400);
        }
        if ($validated['model'] == 'user') {
            $licence->users()->detach($validated['model_id']);
            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licenceId,
                'action' => 'unassign',
                'model' => $validated['model'],
                'model_id' => $validated['model_id']
            ]);
            return response()->json([
                'message' => 'Success'
            ], 200);
        } else if ($validated['model'] == 'asset') {
            $licence->assets()->detach($validated['model_id']);
            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licenceId,
                'action' => 'unassign',
                'model' => $validated['model'],
                'model_id' => $validated['model_id']
            ]);
            return response()->json([
                'message' => 'Success'
            ], 200);
        }
        return response()->json([
            'message' => 'Unnkown model ' . $validated['model']
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
        //
    }
}
