<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{   
    public function create()
    {
        return view('register');
    }    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'skills' => 'nullable|array',
            'nid' => 'nullable|string|max:50',
            'sms_opt_in' => 'nullable|boolean',
            'locale' => 'required|string|in:en,bn',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['sms_opt_in'] = $request->has('sms_opt_in');
        $validatedData['role'] = 'worker';

        $user = User::create($validatedData);

        Auth::login($user);

        return redirect('/')->with('success', 'Welcome to RuralConnect! Your account has been created successfully.');
    }

    public function login()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginId = $request->input('login_id');
        $password = $request->input('password');

        $fieldType = filter_var($loginId, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $fieldType => $loginId,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // FIX: Removed the admin-specific override redirect here.
            // Now all users, including admins, go directly to the homepage.
            return redirect()->intended('/')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'login_id' => 'The provided credentials do not match our records.',
        ])->onlyInput('login_id');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'You have been logged out.');
    }
}