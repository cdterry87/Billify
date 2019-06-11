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
        $data = Bill::select(DB::raw('day as x'), DB::raw('sum(amount) as y'))->where('user_id', Auth::user()->id)
            ->groupBy('day')->get()
            ->toArray();

        $data[] = [
            'x' => 0,
            'y' => 0,
        ];

        $data[] = [
            'x' => 31,
            'y' => 0,
        ];

        array_multisort($data);

        return response()->json($data);
    }
}
