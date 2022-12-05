<?php

namespace App\Http\Controllers;

use App\Models\LicenceHistory;
use App\Models\Licence;
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
            'reassignable' => 'boolean|required'
        ]);

        $licence = Licence::create([
            'name' => $validated['name'],
            'manufacturer_id' => $validated['manufacturer_id'],
            'category_id' => $validated['category_id'],
            'product_key' => $validated['product_key'],
            'email' => $request->email,
            'slots' => $validated['slots'],
            'expiration_date' => $request->expiration_date,
            'reassignable' => $validated['reassignable']
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
        return Licence::select('id', 'name', 'manufacturer_id', 'category_id', 'product_key', 'email', 'expiration_date', 'reassignable', 'slots')
            ->with('manufacturer')
            ->with('category')
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

    public function showHistory($id)
    {
        return LicenceHistory::select('id', 'user_id', 'licence_id', 'action', 'model', 'model_id')
            ->where('licence_id', $id)
            ->get();
    }
}
