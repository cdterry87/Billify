<?php

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;
use App\Traits\WithAlerts;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;

class Home extends Component
{
    use WithAlerts;

    private $chartJsonConfig = [
        'title' => [
            'style' => [
                'fontSize' => '16px',
                'color' => '#898989'
            ]
        ],
        'legend' => [
            'fontSize' => '14px',
            'fontWeight' => 'bold',
            'labels' => [
                'colors' => [
                    '#898989'
                ]
            ]
        ],
        'tooltip' => [
            'theme' => 'dark',
        ],
        'xaxis' => [
            'labels' => [
                'style' => [
                    'fontSize' => '12px',
                    'colors' => '#898989'
                ]
            ]
        ],
        'yaxis' => [
            'labels' => [
                'style' => [
                    'fontSize' => '12px',
                    'colors' => '#898989'
                ]
            ]
        ]
    ];

    public function render()
    {
        // Get income summary
        $yearlyIncome = auth()->user()->getYearlyIncome();
        $monthlyIncome = auth()->user()->getMonthlyIncome();
        $biWeeklyIncome = auth()->user()->getBiWeeklyIncome();
        $weeklyIncome = auth()->user()->getWeeklyIncome();

        // Get notifications
        $notifications = $this->getNotifications();

        // Get charts
        $incomeVsBillsChart = $this->getIncomeVsBillsChart();
        $topCategoriesChart = $this->getTopCategoriesChart();
        $billsByDayChart = $this->getBillsByDayChart();

        return view('livewire.home', [
            'yearlyIncome' => $yearlyIncome,
            'monthlyIncome' => $monthlyIncome,
            'biWeeklyIncome' => $biWeeklyIncome,
            'weeklyIncome' => $weeklyIncome,
            'notifications' => $notifications,
            'incomeVsBillsChart' => $incomeVsBillsChart,
            // 'topCategoriesChart' => $topCategoriesChart,
            'billsByDayChart' => $billsByDayChart
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

    public function getIncomeVsBillsChart()
    {
        $income = auth()->user()->income;
        $billsTotal = auth()->user()->currentMonthBills()->sum('amount');

        return (new PieChartModel())
            ->withDataLabels()
            ->addSlice('Income', $income, '#047857')
            ->addSlice('Bills', $billsTotal, '#be123c')
            ->setJsonConfig($this->chartJsonConfig);
    }

    public function getTopCategoriesChart()
    {
        //
    }

    public function getBillsByDayChart()
    {
        $bills = auth()->user()->currentMonthBills()
            ->select('day', DB::raw('sum(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $chart = (new LineChartModel())
            ->withDataLabels()
            ->multiLine()
            ->setJsonConfig($this->chartJsonConfig);

        foreach ($bills as $label => $value) {
            $chart->addSeriesPoint('Payments This Month', $label, $value);
        }

        return $chart;
    }
}
