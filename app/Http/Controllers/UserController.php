<?php

namespace App\Http\Controllers;

use App\Mail\FirstLoginCredentials;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'role' => 'string|nullable',
            'per_page' => 'integer|max:100|nullable',
            'sort' => 'in:id,name,surname,email',
            'order' => 'in:asc,desc'
        ]);

        $user = User::query()
            ->select(['id', 'name', 'surname', 'phone_number', 'email'])
            ->with(["roles" => function ($a) {
                $a->select(['id', 'name']);
            }]);

        $user->orderBy($validated['sort'] ?? 'id', ($validated['order'] ?? 'asc'));

        if ($request->role != null) {
            $user = $user->whereHas("roles", function ($q) use ($validated) {
                $q->where("name", $validated['role']);
            });
        }

        $response = $user->paginate($validated['per_page'] ?? 25)->toArray();

        foreach ($response['data'] as $key => $value) {
            $tempUser = User::find($value['id']);
            if ($tempUser->email_verified_at == null) {
                $response['data'][$key]['activated'] = false;
            } else {
                $response['data'][$key]['activated'] = true;
            }
        }
        return $response;
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
        $this->validate($request, [
            'name' => 'string|required',
            'surname' => 'string|required',
            'email' => 'required|email|unique:users,email',
            'roles' => 'array|required'
        ]);

        $input = $request->all();
        //$input['password'] = Hash::make(Str::random(16));
        $input['password'] = Hash::make('password');

        $user = User::create($input);
        $user->syncRoles($request->roles);

        Mail::to($request->email)
            ->send(new FirstLoginCredentials($request->email, 'password'));

        return response()->json([
            'message' => 'User was created successfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::where('id', $id)->select(['id', 'name', 'surname', 'phone_number', 'email'])->with(['roles', 'assets'])->first();
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
            'email' => 'nullable|email',
            'roles' => 'array|required'
        ]);
        if (($request->email == null) || ($request->email == User::where('id', $id)->first()->email)) {
            User::where('email', $request->email)->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone_number' => $request->phone_number
            ]);
        } else {
            if (User::where('email', $request->email)->first() == null) {
                User::where('id', $id)->update([
                    'name' => $request->name,
                    'surname' => $request->surname,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number
                ]);
            } else {
                return response()->json([
                    'message' => 'The email has already been taken'
                ], 400);
            }
        }
        User::where('id', $id)
            ->first()
            ->syncRoles($request->roles);

        return response()->json([
            'message' => 'User was updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (User::where('id', $id)->first()) {
            User::where('id', $id)->delete();

            return response()->json([
                'message' => 'User was deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'massegae' => 'There is no user with id = ' . $id
            ]);
        }
    }

    public function activateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'token' => 'string|required',
            'password' => 'string|required|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->errors()],
                400
            );
        }

        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->token
        ])) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Account is already activated'
            ], 405);
        }
        User::where('email', $request->email)->first()->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'Account is now activated'
        ], 200);
    }

    public function setUserDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|max:15',
        ]);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->errors()],
                400
            );
        }
        if ($request->avatar == null) {
            User::where('id', auth()->user()->id)
                ->update([
                    'phone_number' => $request->phone_number
                ]);
        } else {
            $image = $request->file('avatar');
            $filename = uniqid() . '.' . $request->avatar->extension();
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(300, 300);
            $image_resize->save('storage/avatars/' . $filename);

            User::where('id', auth()->user()->id)
                ->update([
                    'phone_number' => $request->phone_number,
                    'avatar' => $filename
                ]);
        }
        return response()->json([
            'message' => 'Details were updated successfuly'
        ]);
    }

    public function showAvatar($id)
    {
        $avatarName = User::where('id', $id)
            ->first();
        if ($avatarName == null) {
            return response()->json([
                'message' => 'user with id ' . $id . ' does not exist'
            ], 400);
        }
        if ($avatarName->avatar == null) {
            return null;
        }
        return Storage::response('/public/avatars/' . $avatarName->avatar);
    }

    public function removeAvatar(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        Storage::response('/public/avatars/' . $user->avatar);
        $user->avatar = null;
        $user->save();
        return response()->json([
            'message' => 'Avatar was removed successfully'
        ]);
    }

    public function isActivated(User $user)
    {
        if ($user->email_verified_at != null) {
            return true;
        }
        return false;
    }

    public function removeRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'integer|required',
            'role' => 'string|required'
        ]);
        if (!Role::where('name', $validated['role'])->first()) {
            return response()->json([
                'message' => 'There is no role ' . $validated['role']
            ], 400);
        }
        $user = User::find($validated['user_id']);
        if ($user) {
            $user->removeRole($validated['role']);
            return response()->json([
                'message' => 'Role ' . $validated["role"] . ' has been removed form user with id ' . $validated['user_id']
            ]);
        }
        return response()->json([
            'message' => 'There is no user with id ' . $validated['user_id']
        ], 400);
    }

    public function massAssignRoles(Request $request)
    {
        $validated = $request->validate([
            'users' => 'array|required',
            'users.*' => 'integer|distinct',
            'id' => 'string|required'
        ]);

        $role = Role::where('name', $validated['id'])->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role with id ' . $validated['id'] . ' does not exist'
            ]);
        }

        foreach ($validated['users'] as $user_id) {
            User::find($user_id)->syncRoles([$validated['id']]);
        }

        return response()->json([
            'message' => 'Success'
        ]);
    }
}
