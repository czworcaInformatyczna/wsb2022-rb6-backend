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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::with('roles')->paginate(25);
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'string|required'
        ]);

        $input = $request->all();
        //$input['password'] = Hash::make(Str::random(16));
        $input['password'] = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; //password

        $user = User::create($input);
        $user->assignRole($request->input('role'));
    
        Mail::to($request->email)
                ->send(new FirstLoginCredentials($request->email, $input['password']));
        
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
        return User::where('id', $id)->first()->roles;
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
            'email' => 'required|email|unique:users,email',
            'role' => 'string|required'
        ]);

        User::where('email', $request->email)
            ->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'role' => $request->roles,
            'phone_number' => $request->phone_number
            ]);

    
        User::where('email', $request->email)
            ->first()
            ->syncRoles([$request->role]);;
        
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
        return User::where('id', $id)
            ->delete();
    }

    public function activateAccount(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'new_password' => 'string|required|confirmed'
        ]);
        if($validator->fails()){
            return response()->json(
                ['errors' => $validator->errors()]
            ,400);
        }

        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json([
            'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if(!is_null($user->email_verified_at)){
            return response()->json([
                'message' => 'Account is already activated'
            ],405);
        }
        User::where('email', $request->email)->first()->update([
            'password' => Hash::make($request->new_password),
            'email_verified_at' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'Account is now activated'
        ], 200);
    }

    public function setUserDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|max:15',
        ]);
        if($validator->fails()){
            return response()->json(
                ['errors' => $validator->errors()]
            ,400);
        }
        if($request->avatar == null){
            User::where('id', auth()->user()->id)
                ->update([
                    'phone_number' => $request->phone_number
                ]);
        }
        else{
            $image = $request->file('avatar');
            $filename = uniqid().'.'.$request->avatar->extension();
            $image_resize = Image::make($image->getRealPath());              
            $image_resize->resize(300, 300);
            $image_resize->save('storage/avatars/'.$filename);

            User::where('id', auth()->user()->id)
                ->update([
                    'phone_number' => $request->phone_number,
                    'avatar' => $filename
                ]);
        }
        return 'Details were updated successfuly';
    }

    public function showAvatar(Request $request){
        $avatarName = User::where('id', $request->user_id)
            ->first();
        if($avatarName == null){
            return response()->json([
                'message' => 'user with id '.$request->user_id.' does not exist'
            ], 400);
        }
        if($avatarName->avatar == null){
            return null;
        }
        return Storage::response('/public/avatars/'.$avatarName->avatar);
    }

    public function removeAvatar(Request $request){
        $user = User::where('id', $request->user_id)->first();
        Storage::response('/public/avatars/'.$user->avatar);
        $user->avatar = null;
        $user->save();
        return response()->json([
            'message' => 'Avatar was removed successfully'
        ]);
    }
}
