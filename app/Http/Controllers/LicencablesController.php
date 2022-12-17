<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Asset;
use App\Models\Licence;
use App\Models\User;
use App\Models\LicenceHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function indexAll(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'search' => 'string|nullable|min:1|max:30',
            'sort' => [
                'string',
                Rule::in(['id', 'created_at', 'updated_at', 'name', 'email', 'slots'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'model' => [
                'string',
                Rule::in(['assets', 'users']),
            ],
            'model_id' => [
                'integer',
                'exclude_without:model'
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);
        $model = Licence::query();

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $model = $model->where(function ($query) use ($validated) {
                $query->where('name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            // $model = $model->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if ($validated['model_id'] ?? false) {
            $model = $model->whereRelation($validated['model'], $validated['model'] . '.id', $validated['model_id']);
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new GenericExport($model))->download('licencable.xlsx');
        }

        return $model->paginate($validated['per_page'] ?? 10);
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
        if ($licence->slots <= $sum) {
            return response()->json([
                'message' => 'Exceeded maximum licence slots'
            ], 400);
        }
        if ($validated['model'] == 'user') {
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
        } else if ($validated['model'] == 'asset') {
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
        if ($licence->reassignable == false) {
            return response()->json([
                'message' => 'This licence is not reassignable'
            ]);
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
