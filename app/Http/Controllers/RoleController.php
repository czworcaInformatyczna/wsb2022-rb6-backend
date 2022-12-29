<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Exports\GenericExport;
use App\Models\User;

use function PHPSTORM_META\map;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Manage Roles')->only(['index', 'show', 'store', 'update', 'destroy', 'rolesWithUsers']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|min:2|max:100',
            'search' => 'string|nullable|min:1|max:30',
            'sort' => 'nullable|in:id,name',
            'order' => 'nullable|in:asc,desc',
            'export' => 'boolean'
        ]);

        $roles = Role::query()->select('id', 'name');

        if ($request->export) {
            return (new GenericExport($roles))->download('roles.xlsx');
        }

        $roles = $roles->with(['permissions' => function ($a) {
            $a->select('id', 'name');
        }]);


        if ($request->search) {
            $roles = $roles->where(function ($query) use ($validated) {
                $query->Where('id', 'like', "%{$validated['search']}%")
                    ->orWhere('name', 'like', "%{$validated['search']}%")
                    ->orWhereRelation('permissions', 'name', 'like', "%{$validated['search']}%");
            });
        }

        $roles = $roles->orderBy($validated['sort'] ?? 'id', ($validated['order'] ?? 'asc'));
        return $roles->paginate($validated['per_page'] ?? 25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'permissions' => 'array|required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->errors()],
                400
            );
        }

        if (Role::where('name', $request->name)->first()) {
            return response()->json([
                'error' => 'Role ' . $request->name . ' already exists'
            ], 400);
        }

        $permissionsErrors = [];
        foreach ($request->permissions as $permission) {
            if (!Permission::where('name', $permission)->first()) {
                $permission = array_push($permissionsErrors, $permission);
            }
        }
        if (sizeof($permissionsErrors) != 0) {
            return response()->json([
                'message' => 'One or more of chosen permisions does not exist',
                'non exisiting permissions' => $permissionsErrors
            ]);
        }

        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web'
        ]);
        $role->syncPermissions($request->input('permissions'));

        return response()->json([
            'message' => 'Role created successfully'
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
        $role = Role::select(['id', 'name'])->find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->select(['id', 'name'])
            ->get();

        return compact('role', 'rolePermissions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'array',
        ]);

        $role = Role::find($id);
        if ($role == null) {
            return response()->json([
                'message' => 'There is no role with id ' . $id
            ]);
        }
        if ($role->name != $request->name) {
            if (Role::where('name', $request->name)->first()) {
                return response()->json([
                    'error' => 'Role ' . $request->name . ' already exists'
                ], 400);
            }
        }

        $permissionsErrors = [];
        foreach ($request->permissions as $permission) {
            if (!Permission::where('name', $permission)->first()) {
                array_push($permissionsErrors, $permission);
            }
        }

        if (sizeof($permissionsErrors) != 0) {
            return response()->json([
                'message' => 'One or more of chosen permisions does not exist',
                'non exisiting permissions' => $permissionsErrors
            ]);
        }


        if ($role->name == 'Super Admin') {
            return response()->json([
                "message" => "This role can't be edited"
            ]);
        }

        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permissions'));

        return response()->json([
            'message' => 'Role updated successfully'
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
        $role =  Role::find($id);
        if ($role->name == 'Super Admin') {
            return response()->json([
                "message" => "This role can't be deleted"
            ]);
        }
        $role->delete();

        return response()->json([
            'message' => 'Role removed successfully'
        ]);
    }

    public function rolesWithUsers(Request $request, $id)
    {
        $validated = $request->validate([
            'per_page' => 'integer|nullable|max:100'
        ]);

        $role = Role::select('name')->find($id);
        if ($role == null) {
            return response()->json([
                'message' => 'There is no role with id ' . $id
            ]);
        }
        $users = User::select('id', 'name', 'surname', 'phone_number', 'avatar', 'email')->whereHas("roles", function ($q) use ($role) {
            $q->where("name", $role->name);
        })->paginate($validated['per_page'] ?? 25);
        return $users;
    }
}
