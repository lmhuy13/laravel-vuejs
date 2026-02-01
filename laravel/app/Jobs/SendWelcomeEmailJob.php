<?php

namespace App\Jobs;

use App\Mail\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    public User $user;
    public string $temporaryPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send(new SendWelcomeEmail($this->user, $this->temporaryPassword));
            
            \Log::info("✓ Welcome email sent to user: {$this->user->email}", [
                'user_id' => $this->user->id,
                'user_name' => $this->user->name,
            ]);
        } catch (\Throwable $exception) {
            \Log::warning("⚠ Email send failed for {$this->user->email}, sleeping 30s before retry: " . $exception->getMessage());
            sleep(30);
            throw $exception;
        }
    }

    /**
     * Get the queue that the job should be dispatched to.
     */
    public function queue(): string
    {
        return 'mail';
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Failed to send welcome email to {$this->user->email}: " . $exception->getMessage());
    }
}
