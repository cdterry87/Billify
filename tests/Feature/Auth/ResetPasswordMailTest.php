<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Ada', 'email' => 'ada@example.com']);
    $this->mail = (new ResetPasswordNotification('the-token'))->toMail($this->user);
});

it('is queued', function () {
    expect(new ResetPasswordNotification('the-token'))->toBeInstanceOf(ShouldQueue::class);

    Queue::fake();
    $this->user->sendPasswordResetNotification('the-token');

    Queue::assertPushed(SendQueuedNotifications::class, fn (SendQueuedNotifications $job) => $job->notification instanceof ResetPasswordNotification);
});

it('has a branded subject', function () {
    expect($this->mail->subject)->toBe('Reset your '.config('app.name').' password');
});

it('renders the HTML and plain-text versions', function () {
    $url = url('/reset-password/the-token?email=ada%40example.com');
    $html = (string) $this->mail->render();
    $text = view('mail.reset-password-text', $this->mail->viewData)->render();

    foreach ([$html, $text] as $body) {
        expect($body)
            ->toContain('Hi Ada,')
            ->toContain('expires in 60 minutes')
            ->toContain('not intended for public use');
    }

    expect($html)->toContain('href="'.e($url).'"')
        ->and($text)->toContain($url);
});
