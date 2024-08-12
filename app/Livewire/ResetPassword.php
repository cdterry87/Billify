<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Session;

class ResetPassword extends Component
{
    public $token, $email;
    public $password, $password_confirmation;

    public function mount($token)
    {
        $passwordResetToken = \App\Models\PasswordResetToken::where('token', $token)->first();

        // If the token does not exist, return a 404 error
        if (!$passwordResetToken) abort(404);

        // Set the email and token properties
        $this->email = $passwordResetToken->email;
        $this->token = $passwordResetToken->token;
    }

    public function render()
    {
        return view('livewire.reset-password')
            ->layout('components.layouts.guest');
    }

    public function submit()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Update the user's password
        $user = User::where('email', $this->email)->first();
        $user->update([
            'password' => bcrypt($this->password),
        ]);

        // Delete the password reset token
        PasswordResetToken::where('email', $this->email)->delete();

        Session::flash('alert-type', 'success');
        Session::flash('alert-message', 'Your password has been reset successfully. Please login.');

        // Redirect the user to the login page
        return redirect()->route('login');
    }
}
