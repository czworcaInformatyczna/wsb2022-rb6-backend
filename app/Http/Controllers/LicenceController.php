<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\LicenceHistory;
use App\Models\Licence;
use App\Models\LicenceFile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LicenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Licence::with('manufacturer')
            ->with('category')
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
            'name' => 'string|required|min:2|max:32',
            'manufacturer_id' => 'integer|required|exists:manufacturers,id',
            'category_id' => 'integer|required|exists:licence_categories,id',
            'product_key' => 'string|required',
            'slots' => 'integer|required|min:1',
            'email' => 'email',
            'expiration_date' => 'date',
            'reassignable' => 'in:true,false|required'
        ]);

        $licence = Licence::create([
            'name' => $validated['name'],
            'manufacturer_id' => $validated['manufacturer_id'],
            'category_id' => $validated['category_id'],
            'product_key' => $validated['product_key'],
            'email' => $request->email,
            'slots' => $validated['slots'],
            'expiration_date' => $request->expiration_date,
            'reassignable' => ($validated['reassignable'] == 'true') ? 1 : 0
        ]);

        LicenceHistory::create([
            'user_id' => auth()->user()->id,
            'licence_id' => $licence->id,
            'action' => 'create',
            'model' => 'Licence',
            'model_id' => $licence->id
        ]);

        return response()->json([
            'message' => 'Licence was created successufly'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $licence = Licence::select('id', 'name', 'manufacturer_id', 'category_id', 'product_key', 'email', 'expiration_date', 'reassignable', 'slots')
            ->with('manufacturer')
            ->with('category')
            ->find($id);
        $remainingSlots = $licence->slots - $licence->users()->count() - $licence->assets()->count();
        $response = $licence->toArray();
        $response['remaining_slots'] = $remainingSlots;
        return $response;
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
            'name' => 'required|max:32',
            'manufacturer_id' => 'integer|required|exists:manufacturers,id',
            'category_id' => 'integer|required|exists:licence_categories,id',
            'product_key' => 'string|required',
            'email' => 'email',
            'slots' => 'integer|required|min:1',
            'expiration_date' => 'date',
            'reassignable' => 'boolean|required'
        ]);

        $licence = Licence::find($id);
        if ($licence) {
            $licence->update([
                'name' => $validated['name'],
                'manufacturer_id' => $validated['manufacturer_id'],
                'category_id' => $validated['category_id'],
                'product_key' => $validated['product_key'],
                'email' => $request->email,
                'slots' => $validated['slots'],
                'expiration_date' => $request->expiration_date,
                'reassignable' => $validated['reassignable']
            ]);
            $licence->save();

            LicenceHistory::create([
                'user_id' => auth()->user()->id,
                'licence_id' => $licence->id,
                'action' => 'edit',
                'model' => 'Licence',
                'model_id' => $licence->id
            ]);

            return response()->json([
                'message' => 'Licence was updated successufly'
            ]);
        }
        return response()->json([
            'message' => 'There is no licence with id ' . $id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $licence = Licence::where('id', $id)->first();
        if ($licence) {
            $licence->delete();
            return response()->json([
                'message' => 'licence has been deleted'
            ], 200);
        }
        return response()->json([
            'message' => 'there is no licence with id ' . $id
        ], 400);
    }

    public function showHistory(Request $request, $id)
    {
        $validated = $request->validate([
            'per_page' => "integer|min:1|max:100"
        ]);
        $licenceHistory = LicenceHistory::select('id', 'user_id', 'licence_id', 'action', 'model', 'model_id', 'created_at')
            ->where('licence_id', $id)
            ->with('user')
            ->paginate($validated['per_page'] ?? 25)->toArray();

        foreach ($licenceHistory['data'] as $key => $history) {
            if ($history['model'] == 'Licence') {
                $licenceHistory['data'][$key]['licencable'] = Licence::find($history['model_id']);
            } elseif ($history['model'] == 'LicenceFile') {
                $licenceHistory['data'][$key]['licencable'] = LicenceFile::find($history['model_id']);
            } elseif ($history['model'] == 'assets' || 'asset') {
                $licenceHistory['data'][$key]['licencable'] = Asset::find($history['model_id']);
            } elseif ($history['model'] == 'users' || 'user') {
                $licenceHistory['data'][$key]['licencable'] = User::find($history['model_id']);
            }
        }

        return $licenceHistory;
    }
}
