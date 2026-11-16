<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ProfileInstaller;
use App\Models\User;
use Tests\TestCase;

/**
 * Phase 6c: the four "view own X" capabilities profiles have granted
 * to a customer role since Phase 2-4, finally with something real to
 * scope against. A customer is a real User row, same table and login
 * as Owner/Staff — just assigned 'customer' instead.
 */
class CustomerTierTest extends TestCase
{
    private function asCustomer(): User
    {
        $customer = User::factory()->create();
        $customer->roles()->attach('customer');

        return $customer;
    }

    public function test_a_customer_only_ever_sees_their_own_invoices(): void
    {
        app(ProfileInstaller::class)->install('small-business', 'owner');

        $mine = $this->asCustomer();
        $someoneElse = $this->asCustomer();

        $this->postJson('/api/modules/invoicing/invoices', [
            'customer_name' => 'Mine', 'amount_cents' => 1000, 'user_id' => $mine->id,
        ])->assertCreated();
        $this->postJson('/api/modules/invoicing/invoices', [
            'customer_name' => 'Not mine', 'amount_cents' => 2000, 'user_id' => $someoneElse->id,
        ])->assertCreated();

        $this->actingAs($mine);

        $response = $this->getJson('/api/modules/invoicing/my-invoices')->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['customer_name' => 'Mine']);
        $response->assertJsonMissing(['customer_name' => 'Not mine']);

        // The customer role only ever got invoicing.view — the full
        // Staff/Owner list stays out of reach.
        $this->getJson('/api/modules/invoicing/invoices')->assertForbidden();
    }

    public function test_a_customer_can_submit_and_see_only_their_own_application(): void
    {
        app(ProfileInstaller::class)->install('enterprise', 'owner');

        $applicant = $this->asCustomer();
        $this->actingAs($applicant);

        $this->postJson('/api/modules/applications/my-applications', ['application_type' => 'permit'])
            ->assertCreated()
            ->assertJsonFragment(['applicant_name' => $applicant->name, 'email' => $applicant->email]);

        $this->assertDatabaseHas('applications', ['user_id' => $applicant->id, 'application_type' => 'permit']);

        $response = $this->getJson('/api/modules/applications/my-applications')->assertOk();
        $response->assertJsonCount(1);

        $this->postJson('/api/modules/applications/applications', ['applicant_name' => 'Staff-entered'])
            ->assertForbidden();
    }

    public function test_a_customer_can_submit_and_see_only_their_own_workflow_request(): void
    {
        app(ProfileInstaller::class)->install('enterprise', 'owner');

        $requester = $this->asCustomer();
        $this->actingAs($requester);

        $this->postJson('/api/modules/workflows/my-requests', ['title' => 'Need a permit review'])
            ->assertCreated()
            ->assertJsonFragment(['requested_by' => $requester->name]);

        $this->getJson('/api/modules/workflows/my-requests')->assertOk()->assertJsonCount(1);
        $this->getJson('/api/modules/workflows/requests')->assertForbidden();
    }

    public function test_a_student_can_view_assignments_and_submit_and_see_only_their_own_work(): void
    {
        app(ProfileInstaller::class)->install('education', 'owner');

        \Moduloom\Classrooms\Models\Classroom::query()->create(['name' => 'Algebra I']);
        $classroom = \Moduloom\Classrooms\Models\Classroom::first();
        $assignment = \Moduloom\Assignments\Models\Assignment::create([
            'classroom_id' => $classroom->id,
            'title' => 'Homework 1',
        ]);

        $student = $this->asCustomer();
        $this->actingAs($student);

        $this->getJson('/api/modules/assignments/my-assignments')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Homework 1']);

        $this->postJson("/api/modules/assignments/assignments/{$assignment->id}/my-submission", [
            'content' => 'My answer',
        ])->assertCreated()->assertJsonFragment(['student_name' => $student->name]);

        $response = $this->getJson('/api/modules/assignments/my-submissions')->assertOk();
        $response->assertJsonCount(1);

        $this->postJson('/api/modules/assignments/assignments', ['classroom_id' => $classroom->id, 'title' => 'Not allowed'])
            ->assertForbidden();
    }
}
