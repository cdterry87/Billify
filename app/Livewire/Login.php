<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;

    protected function rules()
    {
        return [
            'email' => 'required|email',
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
        return view('livewire.login');
    }

    public function submit()
    {
        $this->validate();

        // Login the user
        Auth::attempt(['email' => $this->email]);




        return redirect()->route('dashboard');
    }
}
