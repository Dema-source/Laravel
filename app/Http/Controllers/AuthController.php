<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        Mail::to($user->email)->send(new WelcomeEmail($user));
        return ResponseHelper::success('Data created successfully', $user);
    }
    public function signIn(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($request->only('email', 'password')))
            return ResponseHelper::error('Invalid email or password', [], 401);
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_Token')->plainTextToken;
        return ResponseHelper::success('Login successfully', [$user, $token]);
    }
    public function signOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseHelper::success('Logout successfully', []);
    }
}
