<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->latest()->get();
        
        return view('admin.users_dashboard', compact('users'));
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->save();
        
        return redirect()->back()->with('success', 'User successfully verified!');
    }
}