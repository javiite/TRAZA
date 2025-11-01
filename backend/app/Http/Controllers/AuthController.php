<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Opcional: un solo token activo por usuario
        $request->user()->tokens()->delete();

        $token = $request->user()->createToken('spa')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $request->user()->only('id','name','email')
        ]);
    }

    // POST /api/logout (requiere token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
