<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
        /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Manufacturer::class, 'manufacturer');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Manufacturer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreManufacturerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManufacturerRequest $request)
    {
        $manufacturer = new Manufacturer($request->validated());
        $saved = $manufacturer->save();
        return response()->json([
            "result" => $saved,
            "model" => $saved ? $manufacturer : null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Manufacturer  $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function show(Manufacturer $manufacturer)
    {
        return $manufacturer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateManufacturerRequest  $request
     * @param  \App\Models\Manufacturer  $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        $manufacturer->fill($request->validated());
        $manufacturer->save();
        return response()->json([
            "result" => "success",
            "manufacturer" => $manufacturer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manufacturer  $manufacturer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manufacturer $manufacturer)
    {
        return response()->json([
            "result" => $manufacturer->delete()
        ]);
    }
}
