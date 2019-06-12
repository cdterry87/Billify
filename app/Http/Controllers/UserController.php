<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user());
    }

    /**
     * Update the user's income.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function income(Request $request)
    {
        $user = Auth::user();
        $user->income = $request->income;

        $status = $user->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Income updated successfully!' : 'Error updating income!'
        ]);
    }

    /**
     * Update the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function account(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->income = $request->income;

        $status = $user->save();

        return response()->json([
            'status' => $status,
            'data' => $user,
            'message' => $status ? 'Account updated successfully!' : 'Error updating account!'
        ]);
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->password);

        $status = $user->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Password changed successfully!' : 'Error changing password!'
        ]);
    }
}
