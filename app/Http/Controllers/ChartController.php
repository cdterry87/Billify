<?php

namespace App\Http\Controllers;

use App\User;
use App\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function incomeVsPayments()
    {
        $income = intval(Auth::user()->income * 2);
        $bills = Bill::where('user_id', Auth::user()->id)->sum('amount');

        $data = [$income, $bills];

        return response()->json($data);
    }

    public function paymentCategories()
    {
        $data = Bill::select('name', 'amount')->where('user_id', Auth::user()->id)->get();

        return response()->json($data);
    }

    public function daily()
    {
        $data = Bill::select(DB::raw('CAST(day as UNSIGNED) as x'), DB::raw('CAST(sum(amount) as UNSIGNED) as y'))->where('user_id', Auth::user()->id)
            ->groupBy('day')->get()
            ->toArray();

        $data[] = [
            'x' => 0,
            'y' => 0,
        ];

        array_multisort($data);

        $daily = [];
        foreach ($data as $row) {
            $daily[] = [
                'x' => intval($row['x']),
                'y' => intval($row['y']),
            ];
        }

        return response()->json($daily);
    }
}
