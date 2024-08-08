<?php

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;

class BillForm extends Component
{
    public $modelId;
    public $name, $category, $description, $amount, $day;
    public $january, $february, $march, $april, $may, $june, $july, $august, $september, $october, $november, $december;

    protected function rules()
    {
        return [
            'name' => 'required|string',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'day' => 'required|numeric',
            'january' => 'nullable|boolean',
            'february' => 'nullable|boolean',
            'march' => 'nullable|boolean',
            'april' => 'nullable|boolean',
            'may' => 'nullable|boolean',
            'june' => 'nullable|boolean',
            'july' => 'nullable|boolean',
            'august' => 'nullable|boolean',
            'september' => 'nullable|boolean',
            'october' => 'nullable|boolean',
            'november' => 'nullable|boolean',
            'december' => 'nullable|boolean'
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Please specify a name for this bill.',
            'category.required' => 'Please select a category.',
            'amount.required' => 'Please specify an amount for this bill.',
            'day.required' => 'Please specify the day of the month this bill is due.'
        ];
    }

    public function mount($modelId = null)
    {
        if ($modelId) {
            $this->edit($modelId);
        }
    }

    public function render()
    {
        $categoryOptions = Bill::getCategoryOptions();

        return view('livewire.bill-form', [
            'categoryOptions' => $categoryOptions
        ]);
    }

    public function edit($modelId)
    {
        $record = Bill::find($modelId);

        if (!$record) {
            $this->dispatch('setModalAlert', 'Bill not found!', 'error');
            return;
        }

        $this->modelId = $record->id;
        $this->name = $record->name;
        $this->category = $record->category;
        $this->description = $record->description;
        $this->amount = $record->amount;
        $this->day = $record->day;
        $this->january = $record->january;
        $this->february = $record->february;
        $this->march = $record->march;
        $this->april = $record->april;
        $this->may = $record->may;
        $this->june = $record->june;
        $this->july = $record->july;
        $this->august = $record->august;
        $this->september = $record->september;
        $this->october = $record->october;
        $this->november = $record->november;
        $this->december = $record->december;
    }

    public function submit()
    {
        $this->validate();

        $record = Bill::updateOrCreate(
            ['id' => $this->modelId],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'amount' => $this->amount,
                'day' => $this->day,
                'january' => $this->january,
                'february' => $this->february,
                'march' => $this->march,
                'april' => $this->april,
                'may' => $this->may,
                'june' => $this->june,
                'july' => $this->july,
                'august' => $this->august,
                'september' => $this->september,
                'october' => $this->october,
                'november' => $this->november,
                'december' => $this->december
            ]
        );

        $this->modelId = $record->id;

        $this->dispatch('setModalAlert', 'Bill saved successfully!', 'success');
        $this->dispatch('refreshBillList');
    }

    public function delete()
    {
        $bill = Bill::query()
            ->where('id', $this->modelId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bill) {
            $this->dispatch('setModalAlert', 'Bill not found!', 'error');
            return;
        }

        $bill->delete();

        $this->resetForm();
        $this->dispatch('setModalAlert', 'Bill deleted successfully!', 'error');
        $this->dispatch('refreshBillList');
    }

    public function resetForm()
    {
        $this->reset();
    }
}
