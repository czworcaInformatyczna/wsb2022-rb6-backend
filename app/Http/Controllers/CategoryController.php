<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\AssetCategory;
use App\Models\AssetComponentCategory;
use App\Models\LicenceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Show Categories')->only('index');
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
                Rule::in(['category_name', 'category_type'])
            ],
            'order' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'export' => [
                Rule::in(['true', 'false', true, false])
            ]
        ]);

        $args = [
            DB::raw('id as category_id'),
            DB::raw('name as category_name')
        ];

        $assetCategory = AssetCategory::select(...$args, ...[DB::raw('"asset_category" as category_type')]);
        $assetComponentCategory = AssetComponentCategory::select(...$args, ...[DB::raw('"asset_component_category" as category_type')]);
        $licenceCategory = LicenceCategory::select(...$args, ...[DB::raw('"licence_category" as category_type')]);


        if ($validated['search'] ?? false) {
            $assetCategory = $assetCategory->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%");
            });

            $assetComponentCategory = $assetComponentCategory->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%");
            });

            $licenceCategory = $licenceCategory->where(function ($query) use ($validated) {
                $query->Where('name', 'like', "%{$validated['search']}%");
            });
        }

        $prepared = $assetCategory->union($assetComponentCategory)->union($licenceCategory);

        if (($validated['sort'] ?? null) !== null) {
            $prepared = $prepared->orderBy($validated['sort'], ($validated['order'] ?? 'asc'));
        }

        if (
            ($validated['export'] ?? null === 'true') ||
            ($validated['export'] ?? null === true)
        ) {
            return (new GenericExport($prepared))->download('category.xlsx');
        }

        return $prepared->paginate($validated['per_page'] ?? 10);
    }
}
