<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    function __construct()
    {
        /*
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
         */
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Role::all();
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
        if($validator->fails()){
            return response()->json(
                ['errors' => $validator->errors()]
            ,400);
        }

        if(Role::where('name', $request->name)->first()){
            return response()->json([
                'error' => 'User '. $request->name .' already exists'
            ], 400);
        }

        $permissionsErrors = [];
        foreach($request->permissions as $permission){
            if(!Permission::where('name', $permission)->first()){
                $permission = array_push($permissionsErrors, $permission);
            }
        }
        if(sizeof($permissionsErrors) != 0){
            return response()->json([
                'message' => 'One or more of chosen permisions does not exist',
                'non exisiting permissions' => $permissionsErrors
            ]);
        }
    
        $role = Role::create(['name' => $request->input('name')]);
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
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$id)
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

        if(!(Role::where('id', $id)->select('name')->first()->value('name') == $request->name)){
            if(Role::where('name', $request->name)->first()){
                return response()->json([
                    'error' => 'User '. $request->name .' already exists'
                ], 400);
            }
        }  

        $permissionsErrors = [];
        foreach($request->permissions as $permission){
            if(!Permission::where('name', $permission)->first()){
                $permission = array_push($permissionsErrors, $permission);
            }
        }
        if(sizeof($permissionsErrors) != 0){
            return response()->json([
                'message' => 'One or more of chosen permisions does not exist',
                'non exisiting permissions' => $permissionsErrors
            ]);
        }
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permissions'));
    
        return response()->json([
            'message' => 'Role created successfully'
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
        Role::find($id)->delete();
        
        return response()->json([
            'message' => 'Role removed successfully'
        ]);
    }
}