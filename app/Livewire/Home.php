<?php

namespace App\Livewire;

use App\Models\Bill;
use Livewire\Component;
use App\Traits\WithAlerts;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;

class Home extends Component
{
    use WithAlerts;

    private $chartJsonConfig = [
        'legend' => [
            'fontSize' => '12px',
            'fontWeight' => 'bold',
            'labels' => [
                'colors' => [
                    '#323232'
                ]
            ]
        ],
        'tooltip' => [
            'theme' => 'light',
        ],
        'xaxis' => [
            'labels' => [
                'show' => false,
            ]
        ],
        'yaxis' => [
            'labels' => [
                'style' => [
                    'fontSize' => '10px',
                    'colors' => '#323232'
                ],
            ]
        ],
        'plotOptions' => [
            'bar' => [
                'dataLabels' => [
                    'position' => 'top',
                ]
            ]
        ],
        'dataLabels' => [
            'enabled' => true,
            'offsetY' => -25,
            'style' => [
                'fontSize' => '16px',
                'colors' => ['#323232'],
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

        return view('livewire.home', [
            'yearlyIncome' => $yearlyIncome,
            'monthlyIncome' => $monthlyIncome,
            'biWeeklyIncome' => $biWeeklyIncome,
            'weeklyIncome' => $weeklyIncome,
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

    public function getIncomeVsBillsChart()
    {
        $income = auth()->user()->getMonthlyIncomeRaw();
        $billsTotal = auth()->user()->currentMonthBills()->sum('amount');

        return (new ColumnChartModel())
            ->withDataLabels()
            ->setTitle('Income vs Bills')
            ->addColumn('Income', $income, '#047857')
            ->addColumn('Expenses', $billsTotal, '#be123c')
            ->setJsonConfig($this->chartJsonConfig);
    }

    public function getTopCategoriesChart()
    {
        // Get currentMonthBills for User, group by category, sum amount for each category, and order by amount desc
        $billCategories = auth()->user()->currentMonthBills()
            ->groupBy('category')
            ->select('category', DB::raw('sum(amount) as total'))
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $categories = [];
        foreach ($billCategories as $billCategory) {
            $categories[$billCategory->category] = $billCategory->total;
        }

        $chart = (new ColumnChartModel())
            ->withDataLabels()
            ->setTitle('Top Bill Categories')
            ->setJsonConfig($this->chartJsonConfig);

        $colors = [
            '#047857',
            '#1d4ed8',
            '#6d28d9',
            '#be123c',
            '#f43f5e',
        ];
        foreach ($categories as $label => $value) {
            $chart->addColumn(ucwords($label), $value, array_shift($colors));
        }

        return $chart;
    }
}
