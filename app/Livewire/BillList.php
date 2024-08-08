<?php

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;
use Livewire\Attributes\On;

class BillList extends Component
{
    public $bills, $billsTotal;
    public $filterCategory;
    public $filterShowing = 'current';

    public function mount()
    {
        $this->getBills();
    }

    public function render()
    {
        $categoryOptions = Bill::getCategoryOptions();

        return view('livewire.bill-list', [
            'categoryOptions' => $categoryOptions
        ]);
    }

    #[On('refreshBillList')]
    public function getBills()
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
        $this->billsTotal = $this->bills->sum('amount');
    }

    public function deleteBill($billId)
    {
        auth()->user()->bills()->where('id', $billId)->delete();

        $this->dispatch('setAlert', 'Bill deleted successfully!', 'error');

        $this->getBills();
    }

    public function resetFilters()
    {
        $this->filterCategory = null;
        $this->filterShowing = 'current';

        $this->getBills();
    }

    public function updatedFilterCategory()
    {
        $this->getBills();
    }

    public function updatedFilterShowing()
    {
        $this->getBills();
    }
}
