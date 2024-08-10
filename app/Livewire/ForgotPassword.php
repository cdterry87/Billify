<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Mail\ForgotPasswordMail;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ForgotPassword extends Component
{
    public $email;

    protected function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.exists' => 'The email address does not exist in our system. Please create an account.',
        ];
    }

    public function render()
    {
        return view('livewire.forgot-password')
            ->layout('components.layouts.guest');
    }

    public function submit()
    {
        $this->validate();

        // Delete any existing password reset tokens for the user
        PasswordResetToken::where('email', $this->email)->delete();

        // Generate a password reset token for the user
        $token = Str::uuid();
        $passwordResetToken = PasswordResetToken::create([
            'email' => $this->email,
            'token' => $token,
            'created_at' => now()
        ]);

        if ($passwordResetToken) {
            // Send forgot password link to the user's email address
            Mail::to($this->email)->send(new ForgotPasswordMail($this->email, $token));

            Session::flash('alert-type', 'success');
            Session::flash('alert-message', 'A password reset link has been sent to your email address.');
        }

        return redirect()->route('login');
    }
}
