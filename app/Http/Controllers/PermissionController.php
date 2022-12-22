<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Show Permissions')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'export' => 'boolean'
        ]);
        $permissions = Permission::query()->select(['id', 'name'])->orderBy('id', 'asc');

        if ($request->export) {
            return (new GenericExport($permissions))->download('permissions.xlsx');
        }
        return $permissions->get();
    }
}
