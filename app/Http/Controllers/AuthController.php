<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function loadlogin()
{
    return view('login');
}

public function loadregister()
{
    return view('register');
}
public function login(Request $request)
{
    try {
        // Validate the form data
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'remember' => 'nullable|in:true,false',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->input('remember', false);

        // Retrieve the user by username
        $user = User::where('username', $username)->first();
        // dd($request);
        if ($user) {
            // Decrypt the stored password
            $decryptedPassword = Crypt::decryptString($user->password);

            // Check if the provided password matches the decrypted password
            if ($password === $decryptedPassword) {
                // Login the user
                Auth::login($user, $remember);
                $user->update(['isactive' => 1]);
                // $user->update(['isactive' => 1]);
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful. Please wait for Redirecting Page!',
                    'redirect' => $this->redirectDash(),
                ]);
            } else {
                // Invalid credentials
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid username or password.',
                ], 401);
            }
        } else {
            // User not found
            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password.',
            ], 401);
        }
    } catch (\Exception $e) {
        \Log::error('Login Validation Error: ' . json_encode($e->getMessage()));
        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again.',
        ], 500);

    }
}


public function redirectDash()
{
    $redirect = '';

    if (Auth::user() && Auth::user()->role == 0) {
        $redirect = '/user/index';
    } else {
        $redirect = '/dashboard'; // Assuming this is the admin dashboard URL
    }

    return $redirect;
}

public function register(Request $request)
{
    $request->validate([
        'fullname' => 'required',
        'username' => 'required',
        'password' => 'required',
        'email' => 'required|email',
    ]);
    try {
        User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);
        return redirect()->route('login')->with('success', 'Registration successful');
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
    }
}

function logoutx(){
    $user = Auth::user();
    $user->update(['isactive' => 0]);
    Session::flush();
    Auth::logout();
    Cookie::queue(Cookie::forget('remember_token'));
    return redirect('/');
 }
}
