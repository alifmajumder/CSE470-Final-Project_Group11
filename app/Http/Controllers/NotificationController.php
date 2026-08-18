<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}