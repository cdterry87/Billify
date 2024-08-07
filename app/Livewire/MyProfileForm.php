<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class MyProfileForm extends Component
{
    public $name, $email, $income, $frequency;
    public $password, $password_confirmation;

    protected function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'income.required' => 'The income field is required.',
            'income.numeric' => 'The income must be a number.',
            'income.min' => 'The income must be at least 0.',
            'income.max' => 'The income may not be greater than 999999.',
            'frequency.required' => 'The frequency field is required.',
            'frequency.in' => 'The selected frequency is invalid.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',
        ];
    }

    public function mount()
    {
        $this->populateForm();
    }

    public function render()
    {
        $frequencyOptions = User::getFrequencyOptions();

        return view('livewire.my-profile-form', [
            'frequencyOptions' => $frequencyOptions,
        ]);
    }

    protected function populateForm()
    {
        $user = auth()->user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->income = $user->income;
        $this->frequency = $user->frequency;
    }

    public function updateProfile()
    {
        $validFrequencyOptions = implode(',', array_keys(User::getFrequencyOptions()));

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'income' => ['required', 'numeric', 'min:0', 'max:999999'],
            'frequency' => ['required', 'string', 'in:' . $validFrequencyOptions],
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'income' => $this->income,
            'frequency' => $this->frequency,
        ]);

        $this->dispatch('setModalAlert', 'Profile updated successfully!', 'success');

        $this->resetForm();
    }

    public function changePassword()
    {
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->dispatch('setModalAlert', 'Password changed successfully!', 'success');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
        $this->income = auth()->user()->income;
        $this->frequency = auth()->user()->frequency;
        $this->password = '';
        $this->password_confirmation = '';
    }
}
