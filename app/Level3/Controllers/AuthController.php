<?php

namespace App\Level3\Controllers;

use App\Level3\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController
{
    public function register(Request $request, TokenService $tokens)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        $token = $tokens->issue();

        DB::table('users')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => md5($data['password']),
            'api_token' => $token,
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        Log::info('login attempt for ' . $email . ' with password ' . $password);

        $user = DB::table('users')
            ->where('email', $email)
            ->where('password', md5($password))
            ->first();

        if ($user === null) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $user->api_token]);
    }

    public function rememberMe(Request $request)
    {
        $value = sha1($request->input('email') . time());
        setcookie('hd_remember', $value, time() + 86400 * 30, '/', '', false, false);

        return response()->json(['remember' => $value]);
    }
}
