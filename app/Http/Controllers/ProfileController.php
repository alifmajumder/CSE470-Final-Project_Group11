<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        $trustScore = $user->averageTrustScore();
        
        return view('profile', compact('user', 'trustScore'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'sms_opt_in' => 'nullable|boolean',
        ]);

        $validated['sms_opt_in'] = $request->has('sms_opt_in');

        $user->update($validated);
        
        return back()->with('success', 'Your profile settings have been updated successfully!');
    }
}