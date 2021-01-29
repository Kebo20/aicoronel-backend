<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    /**
     * Create user
     *
     * @param  [string] userName
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'userName' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'userName' => $request->userName,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }


    /**
     * Login user and create token
     *
     * @param  [string] userName
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string'
            //'remember_me' => 'boolean'
        ]);
        $credentials = request(['name', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        $user = $request->user();
        /*
        if($user->status == 0){
            return response()->json([
                'message' => 'Usuario Inactivo'
            ], 401);
        }
        */
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'user'=>Auth::user()->name,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required',
            'password_rep' => 'required',
        ]);


        $user = User::where('id', Auth::user()->id)->first();
        if($user == null){
            return response()->json([
                'message' => 'Usuario, registro no encontrado.'
            ], 400);
        }
        if($request->password != $request->password_rep){
            return response()->json([
                'message' => 'las contraseñas no coinciden.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada.'
        ], 200);
    }

    public function prueba() {
        return User::all();
    }
}
