<?php

namespace App\Livewire\Forms;

use App\Enums\BillCategory;
use App\Models\Bill;
use App\Models\User;
use App\Support\Money;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BillForm extends Form
{
    public string $name = '';

    public string $category = '';

    public string $description = '';

    public string $amount = '';

    public int|string $due_day = 1;

    /** @var list<string> */
    public array $months = [];

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64'],
            'category' => ['required', Rule::enum(BillCategory::class)],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0.01', 'max:9999999.99'],
            'due_day' => ['required', 'integer', 'between:1,31'],
            'months' => ['array', 'max:12'],
            'months.*' => ['integer', 'distinct', 'between:1,12'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'amount.regex' => 'The amount must be a plain number with at most 2 decimal places.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'due_day' => 'day of month',
            'months.*' => 'month',
        ];
    }

    public function fillFrom(Bill $bill): void
    {
        $this->name = $bill->name;
        $this->category = $bill->category->value;
        $this->description = (string) $bill->description;
        $this->amount = Money::toDecimal($bill->amount_cents);
        $this->due_day = $bill->due_day;
        $this->months = self::monthValues($bill);
    }

    /**
     * Validates and saves the bill, creating it for the user when none is given.
     */
    public function save(User $user, ?Bill $bill = null): Bill
    {
        $this->validate();

        $bill ??= $user->bills()->make();

        $bill->fill([
            'name' => trim($this->name),
            'category' => $this->category,
            'description' => filled($this->description) ? trim($this->description) : null,
            'amount_cents' => Money::toCents($this->amount),
            'due_day' => (int) $this->due_day,
            'months' => $this->months,
        ])->save();

        // Reflect the stored form, so "all twelve" shows as every month.
        $this->months = self::monthValues($bill);

        return $bill;
    }

    /**
     * Months as strings, matching the values checkbox inputs send back.
     *
     * @return list<string>
     */
    private static function monthValues(Bill $bill): array
    {
        return array_map(strval(...), $bill->months ?? []);
    }
}
