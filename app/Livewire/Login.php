<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Login extends Component
{
    public $email, $password;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    protected function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.guest');
    }

    public function submit()
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password
        ])) {
            return redirect()->route('home');
        }

        Session::flash('alert-type', 'error');
        Session::flash('alert-message', 'Your credentials are invalid. Please try again.');

        return redirect()->route('login');
    }
}
