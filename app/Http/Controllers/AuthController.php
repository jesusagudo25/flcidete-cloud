<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMailable;
use App\Mail\UserRegistrationNotification;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
/**
     * Create a new AuthController instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($attr)) {
            return response()->json([
                'status' => 'wrong',
                'message' => 'Invalid login details'
            ], 401);
        }

        if(Auth::user()->active == 0){
            return response()->json([
                'status' => 'wrong',
                'message' => 'Your account is not active'
            ], 401);
        }

        $token = Auth::user()->createToken(Auth::id())->plainTextToken;
        $user = auth()->user();

        $response = [
            'status' => 'success',
            'message' => 'Login successfully',
            'token' => $token,
            'id' => $user->id,
            'role_id' => $user->role_id
            ];

        return response()->json($response,200);
    }

    /**
     * xxxxx
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
         $request->validate([
            'name' => 'required|string|min:3|max:60',
            'email' => 'required|string|email|max:150|unique:users,email',
            'role_id' => 'required|integer',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password)
        ]);

        $users = User::where('role_id', 1)->get();
        
        foreach($users as $user){
            Mail::to($user->email)->send(new UserRegistrationNotification($user));
        }

        $response = [
            'status' => 'success',
            'message' => 'Register successfully',
        ];

        return response()->json($response,200); 
    }

    /**
     * xxxxx
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        $response = [
            'status' => 'success',
            'message' => 'Logged out'
        ];

        return response()->json($response,200);
    }

    /**
     * xxxx
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:150'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'wrong',
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404);
        }

        /* table - generate token */

        $token = Str::random(60);

        PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $token
            ]
        );
        

        /* send email */

        Mail::to($user->email)->send(new ForgotPasswordMailable($token));

        $response = [
            'status' => 'success',
            'message' => 'Reset password link sent to your email',
            'token' => $token,
            'id' => $user->id,
        ];

        return response()->json($response,200);
    }

    /**
     * xxxx
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
            'token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if (!$passwordReset) {
            return response()->json([
                'status' => 'wrong',
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'wrong',
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404);
        }

        $user->password = Hash::make($request->password);

        $user->save();

        $passwordReset->delete();

        /* Return */

        $token = $user->createToken($user->id)->plainTextToken;

        $response = [
            'status' => 'success',
            'message' => 'Register successfully',
            'token' => $token,
            'id' => $user->id,
            'role_id' => $user->role_id
        ];

        return response()->json($response,200);

    }

}
