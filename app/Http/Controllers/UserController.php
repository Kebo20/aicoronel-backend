<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as ResourcesUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index()
    {
        return ResourcesUser::collection(User::where("status", 1)->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'password' => 'required',
            'id_role' => 'required'
        ]);

        $exist_user = User::where('name', $request->name)->first();
        if ($exist_user != null)
            return response()->json([
                'message' => 'Ya existe un usuario con el mismo nombre.'
            ], 400);

        DB::beginTransaction();

        $user = new User();
        $user->name = strip_tags($request->name);
        $user->email = strip_tags($request->email);
        $user->password = Hash::make(strip_tags($request->password));
        $user->id_role = $request->id_role;
        $user->created_by = auth()->id();
        $user->updated_by = auth()->id();
        $user->save();


        DB::commit();
        return response()->json([
            'message' => 'Usuario registrado.',
            'id' => $user->id
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if($request->password!=''){
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'password'=>'required|min:8',
                            'regex:/[a-zA-Z]/',      // must contain at least one lowercase letter
                            //'regex:/[A-Z]/',      // must contain at least one uppercase letter
                            'regex:/[0-9]/',      // must contain at least one digit
                            //'regex:/[@$!%*#?&]/', // must contain a special character
            ]);
        }else{
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                
            ]);
    
        }
      
        DB::beginTransaction();

        $user = User::findOrFail($id);
        $user->name = strip_tags($request->name);
        $user->email = strip_tags($request->email);
        $user->password = Hash::make(strip_tags($request->password));
        $user->id_role = strip_tags($request->id_role);
        $user->updated_by = auth()->id();
        $user->save();


        DB::commit();
        return response()->json([
            'message' => 'Usuario actualizado.',
        ], 200);
    }

    public function destroy($id)
    {

        $user = User::findOrFail($id);
        if ($user == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        DB::beginTransaction();

        $user->status = 0;
        $user->save();

        DB::commit();
        return response()->json([
            'message' => 'Usuario eliminado.',
        ], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        if ($user == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesUser($user);
    }

    public function list(Request $request)
    {
        return ResourcesUser::collection(User::where("name", "like", "%" . $request->search . "%")->get());
    }
}
