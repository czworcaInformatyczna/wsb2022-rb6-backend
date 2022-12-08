<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Exports\ManufacturerExport;
use App\Models\Manufacturer;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:30',
            'search' => 'string|nullable|min:1|max:30',
            'sort' => [
                'string',
                Rule::in(['id', 'created_at', 'updated_at', 'name'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $manufacturer = Manufacturer::query();

        if ($validated['search'] ?? false) {
            // This separated so it doesn't colide with status check
            $manufacturer = $manufacturer->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%");
            });
        }

        if (($validated['sort'] ?? null) !== null) {
            $manufacturer = $manufacturer->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new GenericExport($manufacturer))->download('manufacturer.xlsx');
        }
        return $manufacturer->paginate($validated['per_page'] ?? 10);
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
