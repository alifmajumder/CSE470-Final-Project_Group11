<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAndBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_add_a_worker_to_a_task(): void
    {
        $employer = User::factory()->create(['phone' => '+8801700000010']);
        $worker = User::factory()->create(['phone' => '+8801700000011']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Rice Harvesting',
            'category' => 'farming',
            'description' => 'Harvest rice',
            'wage' => '500',
            'district' => 'Rajshahi',
            'location' => 'North Field',
            'required_workers' => 2,
        ]);

        $response = $this->actingAs($employer)
            ->post("/tasks/{$task->id}/workers", ['worker_identifier' => $worker->phone]);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_workers', [
            'task_id' => $task->id,
            'worker_id' => $worker->id,
            'status' => 'assigned',
        ]);
        $this->assertEquals(1, $task->fresh()->registered_workers);
    }

    public function test_non_owner_cannot_add_workers_to_someone_elses_task(): void
    {
        $employer = User::factory()->create();
        $stranger = User::factory()->create();
        $worker = User::factory()->create(['phone' => '+8801700000012']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Rice Harvesting',
            'category' => 'farming',
            'description' => 'Harvest rice',
            'wage' => '500',
            'district' => 'Rajshahi',
            'location' => 'North Field',
            'required_workers' => 2,
        ]);

        $response = $this->actingAs($stranger)
            ->post("/tasks/{$task->id}/workers", ['worker_identifier' => $worker->phone]);

        $response->assertForbidden();
    }

    public function test_completing_three_jobs_in_a_category_awards_the_badge_and_not_before(): void
    {
        $employer = User::factory()->create();
        $worker = User::factory()->create(['phone' => '+8801700000013']);

        foreach (range(1, 3) as $i) {
            $task = Task::create([
                'employer_id' => $employer->id,
                'title' => "Fishing trip $i",
                'category' => 'fishing',
                'description' => 'Net fishing',
                'wage' => '500',
                'district' => 'Barisal',
                'location' => 'River',
                'required_workers' => 1,
            ]);

            $this->actingAs($employer)
                ->post("/tasks/{$task->id}/workers", ['worker_identifier' => $worker->phone])
                ->assertRedirect();

            $taskWorker = $task->taskWorkers()->first();

            if ($i < 3) {
                $this->assertDatabaseMissing('user_badges', ['user_id' => $worker->id, 'category' => 'fishing']);
            }

            $this->actingAs($employer)
                ->post("/tasks/{$task->id}/workers/{$taskWorker->id}/complete")
                ->assertRedirect();
        }

        $this->assertDatabaseHas('user_badges', [
            'user_id' => $worker->id,
            'category' => 'fishing',
            'badge_label' => 'Verified Fisher',
        ]);
    }

    public function test_employer_can_record_a_payment_and_worker_can_view_and_confirm_it(): void
    {
        $employer = User::factory()->create();
        $worker = User::factory()->create(['phone' => '+8801700000014']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Fishing trip',
            'category' => 'fishing',
            'description' => 'Net fishing',
            'wage' => '500',
            'district' => 'Barisal',
            'location' => 'River',
            'required_workers' => 1,
        ]);

        $this->actingAs($employer)->post("/tasks/{$task->id}/workers", ['worker_identifier' => $worker->phone]);
        $taskWorker = $task->taskWorkers()->first();

        $paymentResponse = $this->actingAs($employer)->post(
            "/tasks/{$task->id}/workers/{$taskWorker->id}/payments",
            ['amount' => '500', 'method' => 'bkash', 'transaction_reference' => 'TX998877']
        );
        $paymentResponse->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'task_id' => $task->id,
            'worker_id' => $worker->id,
            'employer_id' => $employer->id,
            'method' => 'bkash',
            'transaction_reference' => 'TX998877',
        ]);

        $payment = \App\Models\Payment::first();
        $this->assertNotEmpty($payment->receipt_number);

        // Employer can view the receipt
        $this->actingAs($employer)->get("/payments/{$payment->id}/receipt")->assertOk();

        // Worker can view the receipt and confirm it
        $this->actingAs($worker)->get("/payments/{$payment->id}/receipt")->assertOk();
        $this->actingAs($worker)->post("/payments/{$payment->id}/confirm")->assertRedirect();
        $this->assertNotNull($payment->fresh()->worker_confirmed_at);

        // A stranger cannot view the receipt
        $stranger = User::factory()->create();
        $this->actingAs($stranger)->get("/payments/{$payment->id}/receipt")->assertForbidden();

        // Worker's payment history page loads and lists the payment
        $this->actingAs($worker)->get('/my-payments')->assertOk()->assertSee($payment->receipt_number);

        // The transaction ID itself is on the receipt page
        $this->actingAs($worker)->get("/payments/{$payment->id}/receipt")->assertSee('TX998877');
    }

    public function test_bkash_payment_requires_a_transaction_reference(): void
    {
        $employer = User::factory()->create();
        $worker = User::factory()->create(['phone' => '+8801700000015']);

        $task = Task::create([
            'employer_id' => $employer->id,
            'title' => 'Fishing trip',
            'category' => 'fishing',
            'description' => 'Net fishing',
            'wage' => '500',
            'district' => 'Barisal',
            'location' => 'River',
            'required_workers' => 1,
        ]);

        $this->actingAs($employer)->post("/tasks/{$task->id}/workers", ['worker_identifier' => $worker->phone]);
        $taskWorker = $task->taskWorkers()->first();

        $response = $this->actingAs($employer)->post(
            "/tasks/{$task->id}/workers/{$taskWorker->id}/payments",
            ['amount' => '500', 'method' => 'bkash']
        );

        $response->assertSessionHasErrors('transaction_reference');
    }

    public function test_badge_pages_render(): void
    {
        $worker = User::factory()->create();

        $this->actingAs($worker)->get('/my-badges')->assertOk();
        $this->actingAs($worker)->get("/workers/{$worker->id}/badges")->assertOk();
    }
}
