<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarketPrice;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->latest()->get();
        $marketPrices = MarketPrice::latest()->get();
        
        return view('admin.users_dashboard', compact('users', 'marketPrices'));
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = true;
        $user->save();
        
        return redirect()->back()->with('success', 'User successfully verified!');
    }
}