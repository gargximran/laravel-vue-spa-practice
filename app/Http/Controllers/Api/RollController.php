<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;

class RollController extends Controller
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
            $roles = Role::where('name', 'LIKE', "%$req->search%")->paginate($req->per_page);
            return response()->json(['role'=> $roles], 200);
        }
        //if there is no search query then execute this
        else{
            return response()->json(["role" => Role::paginate($req->per_page)], 200);
        }
        
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->name;

        $store = $role->save();
    
        if($store){
            return response()->json(['role' => Role::orderBy('created_at', 'desc')->first()], 200);
        }else{
            return response()->json(['insert' => "failed"], 403);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($search)
    {
        
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $role->name = $request->name;
        $role->save();
        return response()->json(['role' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        return $role->delete();
    }

    public function selectedDestroy(Request $req){
        return Role::whereIn("id", $req->selectedRole)->delete();
    }
}
