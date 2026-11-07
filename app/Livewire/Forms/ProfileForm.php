<?php

namespace App\Livewire\Forms;

use App\Enums\PayFrequency;
use App\Models\User;
use App\Support\Money;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProfileForm extends Form
{
    public string $name = '';

    public string $email = '';

    public string $income = '';

    public int|string $pay_frequency = '';

    public string $timezone = '';

    /** Only needed when the email changes. */
    public string $current_password = '';

    public function fillFrom(User $user): void
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->income = Money::toDecimal($user->income_cents);
        $this->pay_frequency = (string) $user->pay_frequency->value;
        $this->timezone = $user->timezone;
        $this->current_password = '';
    }

    public function save(User $user): void
    {
        $emailChanged = mb_strtolower(trim($this->email)) !== mb_strtolower($user->email);

        try {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'income' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0', 'max:999999999.99'],
                'pay_frequency' => ['required', Rule::enum(PayFrequency::class)],
                'timezone' => ['required', 'timezone'],
                'current_password' => $emailChanged ? ['required', 'current_password'] : ['nullable'],
            ], [
                'income.regex' => 'The income must be a plain number with at most 2 decimal places.',
                'current_password.required' => 'Enter your current password to change your email.',
                'current_password.current_password' => 'The password is incorrect.',
            ]);
        } finally {
            $this->current_password = '';
        }

        $user->update([
            'name' => trim($this->name),
            'email' => trim($this->email),
            'income_cents' => Money::toCents($this->income),
            'pay_frequency' => (int) $this->pay_frequency,
            'timezone' => $this->timezone,
        ]);
    }
}
