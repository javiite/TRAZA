<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Login con email/password → regresa token
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $request->user()->tokens()->delete(); // opcional: un token activo por usuario
        $token = $request->user()->createToken('spa')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $request->user()->only('id','name','email')
        ]);
    }

    // Cierra sesión (revoca el token actual)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
    public function me(Request $request)
{
    return response()->json(['data' => $request->user()]);
}

}
