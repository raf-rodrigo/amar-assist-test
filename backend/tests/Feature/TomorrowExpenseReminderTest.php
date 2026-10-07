<?php

namespace Tests\Feature;

use App\Jobs\SendTomorrowExpenseReminder;
use App\Mail\TomorrowExpensesMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TomorrowExpenseReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_job_sends_mailable_to_the_expense_owner(): void
    {
        Carbon::setTestNow('2026-10-06 10:00:00');
        Mail::fake();
        $user = User::factory()->create(['name' => 'Rafael Rodrigo']);
        $category = $user->categories()->create(['description' => 'Contas']);
        $expense = $user->expenses()->create([
            'category_id' => $category->id,
            'date' => '2026-10-07',
            'description' => 'Internet',
            'amount' => 99.90,
        ]);

        (new SendTomorrowExpenseReminder($user->id, '2026-10-07'))->handle();

        Mail::assertSent(TomorrowExpensesMail::class, function (TomorrowExpensesMail $mail) use ($user, $expense): bool {
            return $mail->user->is($user)
                && $mail->expenses->contains('id', $expense->id);
        });
    }

    public function test_mailable_renders_expense_details(): void
    {
        $user = User::factory()->create(['name' => 'Rafael Rodrigo']);
        $category = $user->categories()->create(['description' => 'Contas']);
        $expense = $user->expenses()->make(['description' => 'Internet', 'amount' => 99.90]);
        $expense->setRelation('category', $category);

        $html = view('emails.tomorrow-expenses', [
            'user' => $user,
            'expenses' => collect([$expense]),
            'dueDate' => '2026-10-07',
        ])->render();

        $this->assertStringContainsString('Gerenciador Financeiro', $html);
        $this->assertStringContainsString('Internet', $html);
        $this->assertStringContainsString('R$ 99,90', $html);
    }

    public function test_job_does_not_send_when_there_are_no_expenses_for_the_date(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        (new SendTomorrowExpenseReminder($user->id, '2099-01-01'))->handle();

        Mail::assertNothingSent();
    }
}
