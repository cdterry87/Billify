<?php

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;
use App\Traits\WithAlerts;
use App\Traits\WithCharts;
use Illuminate\Support\Carbon;

class Home extends Component
{
    use WithAlerts;
    use WithCharts;

    public function render()
    {
        // Get income summary
        $yearlyIncome = auth()->user()->getYearlyIncome();
        $monthlyIncome = auth()->user()->getMonthlyIncome();
        $biWeeklyIncome = auth()->user()->getBiWeeklyIncome();
        $weeklyIncome = auth()->user()->getWeeklyIncome();
        $monthlyRemainder = auth()->user()->getMonthlyRemainder();
        $dtiRatio = auth()->user()->getDtiRatio();

        // Get notifications
        $notifications = $this->getNotifications();

        // Get charts
        $incomeVsBillsChart = $this->getIncomeVsBillsChart();
        $topCategoriesChart = $this->getTopCategoriesChart();

        return view('livewire.home', [
            'yearlyIncome' => $yearlyIncome,
            'monthlyIncome' => $monthlyIncome,
            'biWeeklyIncome' => $biWeeklyIncome,
            'weeklyIncome' => $weeklyIncome,
            'monthlyRemainder' => $monthlyRemainder,
            'dtiRatio' => $dtiRatio,
            'notifications' => $notifications,
            'incomeVsBillsChart' => $incomeVsBillsChart,
            'topCategoriesChart' => $topCategoriesChart,
        ]);
    }

    public function getNotifications()
    {
        $startDay = Carbon::now()->format('j');
        $endDay = Carbon::now()->addDays(5)->format('j');

        $bills = Bill::query()
            ->select('name', 'amount', 'day')
            ->where('user_id', auth()->id())
            ->whereBetween('day', [$startDay, $endDay])
            ->orderBy('amount', 'desc')
            ->get();

        $notifications = [];
        foreach ($bills as $bill) {
            $dueInDays = 0;

            $dueInDays = $endDay - $bill->day;
            if ($bill->day >= $startDay) {
                $dueInDays = $bill->day - $startDay;
            }

            $notifications[] = "Your
                <strong>" . $bill->name . "</strong> bill of
                <strong>$" . $bill->amount . "</strong> is due in
                <strong>" . $dueInDays . " day(s)</strong>!";
        }

        return $notifications;
    }
}
