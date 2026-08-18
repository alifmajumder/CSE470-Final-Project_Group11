<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * The logged-in worker's own badge showcase + progress toward the
     * next badge in each category. Their "digital resume".
     */
    public function myBadges(BadgeService $badgeService)
    {
        $progress = $badgeService->progressFor(Auth::user());

        return view('badges.my_badges', [
            'profileUser' => Auth::user(),
            'progress' => $progress,
        ]);
    }

    /**
     * Any logged-in user (typically an employer deciding who to hire) can
     * view another worker's earned badges.
     */
    public function profile(User $user, BadgeService $badgeService)
    {
        $progress = $badgeService->progressFor($user);

        return view('badges.profile', [
            'profileUser' => $user,
            'progress' => $progress,
        ]);
    }
}
