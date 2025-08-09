<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Masukkan Email Anda',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Please enter your password'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'message' => $errors->all(),
                'errors' => [
                    'email' => $errors->first('email'),
                    'password' => $errors->first('password')
                ]
            ], 422);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Incorrect email or password'
            ], 401);
        }

        $clearOldCookie = Cookie::forget('token');

        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'JWT error',
                'message' => $e->getMessage()
            ], 429);
        }

        $newCookie = cookie(
            'token',
            $token,
            60, // menit
            '/',
            null, // atau domain backend
            true, // Secure: true (wajib HTTPS kalau SameSite=None)
            true, // HttpOnly
            false,
            'None' // SameSite=None supaya bisa cross-site
        );

        return response()->json([
            'token' => $token,
            'message' => 'Successfully Login',
        ])->withCookie($clearOldCookie)->withCookie($newCookie);
    }

    public function gettCurrentUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token Expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token Invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            $cookie = Cookie::make('token', '', -1, '/', null, false, true);

            return response()->json(['message' => 'Logged out successfully'])
                ->withCookie($cookie);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'error' => 'Gagal logout, token tidak valid'
            ], 500);
        }
    }
}
