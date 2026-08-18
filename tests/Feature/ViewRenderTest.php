<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_create_page_renders_with_category_dropdown(): void
    {
        $employer = User::factory()->create();
        $this->actingAs($employer)->get('/tasks/create')
            ->assertOk()
            ->assertSee('Job Category')
            ->assertSee('Fishing');
    }

    public function test_my_tasks_page_renders_with_workers_and_payments(): void
    {
        $employer = User::factory()->create();
        $worker = User::factory()->create(['name' => 'Render Test Worker', 'phone' => '+8801777777778']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Boat Repair',
            'category' => 'fishing',
            'description' => 'Fix the fishing boat',
            'wage' => '1000',
            'district' => 'Khulna',
            'location' => 'Harbor',
            'required_workers' => 1,
        ]);

        $task->taskWorkers()->create(['worker_id' => $worker->id, 'status' => 'completed', 'joined_at' => now(), 'completed_at' => now()]);
        $task->increment('registered_workers');

        Payment::create([
            'task_id' => $task->id, 'employer_id' => $employer->id, 'worker_id' => $worker->id,
            'amount' => 1000, 'method' => 'nagad', 'transaction_reference' => 'NG12345', 'paid_at' => now(),
        ]);

        $this->actingAs($employer)->get('/my-tasks')
            ->assertOk()
            ->assertSee('Boat Repair')
            ->assertSee('Render Test Worker')
            ->assertSee('Nagad')
            ->assertSee('unconfirmed');
    }

    public function test_home_page_shows_new_nav_links_when_authed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('My Payments')
            ->assertSee('My Badges');
    }

    public function test_impact_page_displays_dashboard_stats(): void
    {
        $employer = User::factory()->create();
        $worker = User::factory()->create(['phone' => '+8801777777778']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Rice Harvest',
            'category' => 'agriculture',
            'description' => 'Harvest rice in the field',
            'wage' => '1500',
            'district' => 'Barisal',
            'location' => 'Village',
            'required_workers' => 2,
        ]);

        Payment::create([
            'task_id' => $task->id,
            'employer_id' => $employer->id,
            'worker_id' => $worker->id,
            'amount' => 1500,
            'method' => 'cash',
            'paid_at' => now(),
        ]);

        $this->get('/impact')
            ->assertOk()
            ->assertSee('Impact Dashboard')
            ->assertSee('Jobs Created')
            ->assertSee('1')
            ->assertSee('৳1,500.00')
            ->assertSee('Families Supported');
    }
}