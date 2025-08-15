<?php

namespace Tests\Feature;

use App\Events\LoanGenerated;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_loans_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake([LoanGenerated::class]);
        Log::shouldReceive('info')->andReturnTrue();

        \Log::info('Test request URL: ' . url('/api/loans/generate'));
        $response = $this->postJson('/api/loans/generate', [
            'loan_amount' => 1000.00,
            'number_of_loans' => 2,
            'installments_per_loan' => 3,
            'installment_period_minutes' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Loans generated successfully',
                 ]);

        $this->assertDatabaseCount('loans', 2);
        $this->assertDatabaseCount('installments', 6);

        $loans = Loan::all();
        foreach ($loans as $loan) {
            $this->assertEquals(1000.00, $loan->amount);
            $this->assertEquals('active', $loan->status);
            $installments = $loan->installments;
            $this->assertCount(3, $installments);
            foreach ($installments as $installment) {
                $this->assertEqualsWithDelta(333.33, $installment->amount, 0.01);
                $this->assertEquals('pending', $installment->status);
                $this->assertNotNull($installment->due_date);
            }
        }

        Event::assertDispatched(LoanGenerated::class, 2);
    }

    public function test_generate_loans_with_default_installments()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/loans/generate', [
            'loan_amount' => 2000.00,
            'number_of_loans' => 1,
            'installment_period_minutes' => 5,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseCount('loans', 1);
        $this->assertDatabaseCount('installments', 4);

        $loan = Loan::first();
        $this->assertEquals(2000.00, $loan->amount);
        $this->assertEquals('active', $loan->status);
        $this->assertCount(4, $loan->installments);
    }

    public function test_generate_loans_validation_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/loans/generate', [
            'loan_amount' => 'invalid',
            'number_of_loans' => 0,
            'installment_period_minutes' => -1,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['loan_amount', 'number_of_loans', 'installment_period_minutes']);
    }

    public function test_generate_loans_broadcast_event()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::fake([LoanGenerated::class]);

        $response = $this->postJson('/api/loans/generate', [
            'loan_amount' => 1000.00,
            'number_of_loans' => 1,
            'installments_per_loan' => 4,
            'installment_period_minutes' => 10,
        ]);

        $response->assertStatus(201);

        Event::assertDispatched(LoanGenerated::class, function ($event) {
            $loan = $event->loan;
            return $loan->amount == 1000.00 && $loan->installments->count() == 4;
        });
    }
}