<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Register extends Component
{
    public $name, $email, $password, $password_confirmation;
    public $income, $frequency;

    protected function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6|same:password',
            'income' => 'required|numeric|min:1|max:999999999',
            'frequency' => 'required|in:1,12,26,52',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'income.required' => 'The income field is required.',
            'income.numeric' => 'The income field must be a number.',
            'income.min' => 'The income field must be at least 1.',
            'income.max' => 'The income field may not be greater than 999,999,999.',
            'frequency.required' => 'The frequency field is required.',
            'frequency.in' => 'The frequency selected is invalid. Please select from list.',
        ];
    }

    public function render()
    {
        return view('livewire.register');
    }

    public function submit()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'income' => $this->income,
            'frequency' => $this->frequency,
        ]);

        Session::flash('alert-type', 'success');
        Session::flash('alert-message', 'Your account was created successfully.');

        return redirect()->route('login');
    }
}
