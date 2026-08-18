<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskWorker;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Skill Badge System.
 *
 * A worker who completes several jobs in the same category (e.g. "fishing")
 * automatically earns the matching skill badge (e.g. "Verified Fisher"),
 * without any manual admin action -- it's evaluated every time a job is
 * marked completed.
 */
class BadgeService
{
    /**
     * Count this worker's completed jobs in a category, and award the
     * matching badge if they've crossed the threshold and don't already
     * have it. Returns the newly-earned badge, or null if none was earned
     * (already earned, or threshold not yet reached).
     */
    public function evaluateAndAward(User $worker, string $category): ?UserBadge
    {
        $definition = config("skills.badges.{$category}");

        if (! $definition) {
            // No badge exists for this category (e.g. "other").
            return null;
        }

        $alreadyEarned = UserBadge::where('user_id', $worker->id)
            ->where('category', $category)
            ->exists();

        if ($alreadyEarned) {
            return null;
        }

        $completedCount = $this->completedJobCount($worker, $category);

        if ($completedCount < $definition['threshold']) {
            return null;
        }

        return UserBadge::create([
            'user_id' => $worker->id,
            'category' => $category,
            'badge_label' => $definition['label'],
            'jobs_completed_at_award' => $completedCount,
            'earned_at' => Date::now(),
        ]);
    }

    /**
     * How many jobs this worker has completed in a given category.
     */
    public function completedJobCount(User $worker, string $category): int
    {
        return TaskWorker::query()
            ->where('worker_id', $worker->id)
            ->where('status', 'completed')
            ->whereHas('task', fn ($q) => $q->where('category', $category))
            ->count();
    }

    /**
     * Full badge progress for a worker: every badge-eligible category with
     * completed-job count, threshold, earned status, and label/icon. Used
     * for the "My Badges" and worker-profile pages.
     */
    public function progressFor(User $worker): Collection
    {
        $earned = $worker->badges()->get()->keyBy('category');

        return collect(config('skills.badges'))->map(function (array $definition, string $category) use ($worker, $earned) {
            $badge = $earned->get($category);

            return [
                'category' => $category,
                'category_label' => config("skills.categories.{$category}", ucfirst($category)),
                'label' => $definition['label'],
                'icon' => $definition['icon'],
                'threshold' => $definition['threshold'],
                'completed_count' => $this->completedJobCount($worker, $category),
                'earned' => $badge !== null,
                'earned_at' => $badge?->earned_at,
            ];
        })->values();
    }
}
