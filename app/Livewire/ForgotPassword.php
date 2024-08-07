<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken;
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
        return view('livewire.forgot-password');
    }

    public function submit()
    {
        $this->validate();

        PasswordResetToken::updateOrCreate([
            'email' => $this->email,
        ], [
            'token' => Str::uuid(),
            'created_at' => now()
        ]);

        // @todo - Send forgot password link to the user's email address

        Session::flash('alert-type', 'success');
        Session::flash('alert-message', 'A password reset link has been sent to your email address.');

        return redirect()->route('login');
    }
}
