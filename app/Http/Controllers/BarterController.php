<?php

namespace App\Http\Controllers;

use App\Models\Barter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarterController extends Controller
{
    public function index()
    {
        $barters = Barter::with('user')->latest()->get();
        return view('barter.index', compact('barters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'offering' => 'required|string|max:150',
            'seeking' => 'required|string|max:150',
        ]);

        Barter::create([
            'user_id' => Auth::id(),
            'offering' => $request->offering,
            'seeking' => $request->seeking,
        ]);

        return redirect()->back()->with('success', 'Barter offer posted successfully!');
    }

    public function destroy($id)
    {
        $barter = Barter::findOrFail($id);
        if ($barter->user_id === Auth::id() || Auth::user()->role === 'admin') {
            $barter->delete();
            return redirect()->back()->with('success', 'Barter offer removed.');
        }
        abort(403);
    }
}