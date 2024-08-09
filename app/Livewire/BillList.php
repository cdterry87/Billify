<?php

namespace App\Livewire;

use App\Models\Bill;
use App\Traits\WithCurrency;
use Livewire\Component;
use Livewire\Attributes\On;

class BillList extends Component
{
    use WithCurrency;

    public $bills, $billsTotal, $billsCount;
    public $monthlyIncome, $monthlyRemainder;
    public $filterCategory;
    public $filterShowing = 'current';

    public function mount()
    {
        $this->getBillList();
    }

    public function render()
    {
        $categoryOptions = Bill::getCategoryOptions();

        return view('livewire.bill-list', [
            'categoryOptions' => $categoryOptions
        ]);
    }

    #[On('refreshBillList')]
    public function getBillList()
    {
        $this->bills = auth()->user()->bills()
            ->when($this->filterCategory, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($this->filterShowing, function ($query) {
                return $query->where(function ($query) {
                    $currentMonth = strtolower(now()->format('F'));

                    // Get all bills where the current month is true OR where every other month is false
                    $query->where($currentMonth, true)
                        ->orWhere(function ($query) {
                            $query->where('january', false)
                                ->where('february', false)
                                ->where('march', false)
                                ->where('april', false)
                                ->where('may', false)
                                ->where('june', false)
                                ->where('july', false)
                                ->where('august', false)
                                ->where('september', false)
                                ->where('october', false)
                                ->where('november', false)
                                ->where('december', false);
                        });
                });
            })
            ->orderBy('day')->get();
        $this->billsCount = $this->bills->count();

        $billsTotal = $this->bills->sum('amount');
        $monthlyIncome = auth()->user()->getMonthlyIncomeRaw();

        $this->billsTotal = $this->formatCurrency($billsTotal);
        $this->monthlyIncome = $this->formatCurrency($monthlyIncome);
        $this->monthlyRemainder = $this->formatCurrency($monthlyIncome - $billsTotal);
    }

    public function deleteBill($billId)
    {
        auth()->user()->bills()->where('id', $billId)->delete();

        $this->dispatch('setAlert', 'Bill deleted successfully!', 'error');

        $this->getBillList();
    }

    public function resetFilters()
    {
        $this->filterCategory = null;
        $this->filterShowing = 'current';

        $this->getBillList();
    }

    public function updatedFilterCategory()
    {
        $this->getBillList();
    }

    public function updatedFilterShowing()
    {
        $this->getBillList();
    }
}
