<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;

trait WithCharts
{
    protected $chartJsonConfig = [
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
