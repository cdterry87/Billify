<?php

namespace App\Http\Controllers;

use App\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    /**
     * Display a listing of the bills for this month.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Auth::user()->bills()->get();

        $month = strtolower(date('F'));

        $data = [];
        foreach ($bills as $bill) {
            // If the bill is setup to be for certain months and this is not one of those months, do not show it.
            if ($bill['month'] and !$bill[$month]) {
                continue;
            }
            $data[] = $bill;
        }

        return response()->json($data);
    }

    /**
     * Display a listing of all bills.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return response()->json(Auth::user()->bills()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->add(['user_id' => Auth::user()->id]);
        $bill = Bill::create($request->all());

        // $bill = Auth::user()->bills::create([
        //     $request->all()
        // ]);

        return response()->json([
            'status' => (bool)$bill,
            'data' => $bill,
            'message' => $bill ? 'Bill added successfully!' : 'Error adding bill!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        return response()->json($bill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bill $bill)
    {
        $status = $bill->update(
            $request->only(['name', 'description', 'amount', 'day', 'user_id'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Bill updated successfully!' : 'Error updating bill!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {
        $status = $bill->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Bill deleted successfully!' : 'Error deleting bill!'
        ]);
    }
}
