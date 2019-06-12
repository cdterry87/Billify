<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Bill;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function notifications()
    {
        $start = Carbon::now()->format('j');
        $end = Carbon::now()->addDays(5)->format('j');

        $bills = Bill::select('name', 'amount', 'day')->where('user_id', Auth::user()->id)
            ->whereBetween('day', [$start, $end])
            ->orderBy('amount', 'desc')
            ->get()->toArray();

        $notifications = [];
        foreach ($bills as $bill) {
            $notifications[] = [
                'message' => "Your
                    <strong>" . $bill['name'] . "</strong> bill of
                    <strong>$" . $bill['amount'] . "</strong> is due on
                    <strong>" . date('m') . "/" . $bill['day'] . "</strong>!"
            ];
        }

        return response()->json($notifications);
    }
}
