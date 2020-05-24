<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
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

    public function logout(Request $req, $auth){
       
        return $req->user()->tokens()->where("id", $auth)->delete();
    }

    

}
