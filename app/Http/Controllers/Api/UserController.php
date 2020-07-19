<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Role;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        // if there any serch query then execute this
        if(strlen($req->search) > 2){
            $userCollection = new UserCollection(User::where('name', 'LIKE', "%$req->search%")->orWhere("email", "LIKE", "%$req->search%")->paginate($req->per_page));
            $role = Role::orderBy("id", 'asc')->pluck('name')->all();
            return response()->json(["Users"=> $userCollection, "role" => $role]);
            
        }
        //if there is no search query then execute this
        else{
            $userCollection = new UserCollection(User::with("role")->paginate($req->per_page));
            $role = Role::orderBy("id", 'asc')->pluck('name')->all();
            return response()->json(["Users"=> $userCollection, "role" => $role]);
        }
        
    }


    //login user
    public function login(Request $request){
        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();        

     
        if($user){
            if(Hash::check($password, $user->password)){
                $token = $user->createToken('my-token')->plainTextToken;
                $adminCheck = $user->isAdmin();
                return response()->json([
                    'token'=> $token,
                    'isAdmin' => $adminCheck
                ], 200);
            }else{
                return response()->json(["message"=>"Email or Password is wrong"], 403);
            }
        }else{

            return response()->json(["message"=>"Email or Password is wrong"], 403);
        }
    }

    public function verify(Request $req){
        $user = $req->user();
        $isAdmin = $req->user()->role->name;
        
        return response()->json(['user' => $user, 'role' => $isAdmin]);
    }

    //function for logout user
    public function logout(Request $req, $auth){       
        return $req->user()->tokens()->where("id", $auth)->delete();
    }

    //function for store new user
    public function store(Request $req){
        if($req->name && $req->email && $req->password && $req->role){
            $roleName = Role::where('name', $req->role)->first();
            $newUser = new User([
                "name"=> $req->name,
                "email" => $req->email,
                "password" => bcrypt($req->password)

            ]);
            $newUser->role()->associate($roleName);
            $newUser->save();
            $newUser->userProfile()->save(new UserProfile());

            return response()->json(["newUser" => new UserResource($newUser)]);
        }else{
            return response()->json(['Return' => "Wrong detail"], 403);
        }

        
        
    }

    //email verify
    public function emailVerify(Request $req){
        if($req->editedId){
            $userForEdit = User::find($req->editedId);
            if($userForEdit->email != Str::lower($req->email)){
                $emailAvaility = User::where('email', $req->email)->count();
                if($emailAvaility < 1){
                    return response()->json(['exist' => "1"]);
                }else{
                    return response()->json(['exist' => "0"]);
                }
            }else{
                return response()->json(['exist' => "1"]);
            }
        }else{
            $emailAvaility = User::where('email', $req->email)->count();
            if($emailAvaility < 1){
                return response()->json(['exist' => "1"]);
            }else{
                return response()->json(['exist' => "0"]);
            }
        }
    }


    //function for update
    public function update(User $user, Request $req){
        if($req->name && $req->email && $req->role){   
            $roleName = Role::where('name', $req->role)->first();  
            if($roleName){        
            
                $user->name = $req->name;
                $user->email = Str::lower($req->email);
                if($user->id != $req->user()->id){
                    $user->role()->dissociate($user->role);
                    $user->role()->associate($roleName);
                }
                
                $user->save();
                return response()->json(["updateUser"=> new UserResource($user)]);
            }
        }else{
            return response()->json(['Return' => "Wrong detail"], 403);
        }
    }

    //Inline Role Change
    public function inlineRoleChange(Request $request){
        if($request->user()->id == $request->id){
            return response()->json(["user" => new UserResource($request->user()), "right" => 0]);
        }else{
            $newRole = Role::where('name', $request->role)->first();
            $user = User::find($request->id);
            $user->role()->dissociate($user->role());
            $user->role()->associate($newRole);
            $user->save();
            return response()->json(["user" => new UserResource($user), "right" => 1]);          

        }
    }


    //image upload
    public function imageUpload(Request $req){
        $user = User::find($req->user);
        $profile = UserProfile::find($user->userProfile->id);
        if($profile->image != 'image/user/no-image.png'){
            if(File::exists($profile->image)){
                File::delete($profile->image);
            }
        }
        
        $imageFile = $req->photo;
        $imageName = Str::random(20).".png";
        $imageUpload = Image::make($imageFile);
        $imageUpload->encode("png", 90);
        $imageUpload->save(public_path('image\\user\\'.$imageName));


        $profile->image = "image/user/".$imageName;

        $user->userProfile()->save($profile);
        $user->save();



        return response()->json(['newImage' => $profile->image]);

        
    }



    //destroy user 

    public function destroy(User $user){
        $profile = UserProfile::where('user_id', $user->id)->first();
        if($profile->image != 'image/user/no-image.png'){
            if(File::exists($profile->image)){
                File::delete($profile->image);
            }
        }
        $profile->delete();
        return $user->delete();
    }

    //destroy selected
    public function deleteSelected(Request $req){
        foreach($req->selectedUser as $userToDelete){
            $profile = UserProfile::where('user_id', $userToDelete)->first();
            if($profile->image != 'image/user/no-image.png'){
                if(File::exists($profile->image)){
                    File::delete($profile->image);
                }
            }
            $profile->delete();

        }
        return User::whereIn('id', $req->selectedUser)->delete();
    }

}
