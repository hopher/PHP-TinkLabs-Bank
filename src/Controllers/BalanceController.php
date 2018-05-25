<?php

namespace TinkLabs\Bank\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class BalanceController extends Controller
{

    /**
     * Get current balance
     */
    public function show(Request $request)
    {
        $uid = $request->input('uid');

        $balance = app('bank')->getCurrentBalance($uid);
        if ($balance) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'balance' => $balance,
                ],
            ]);
        }

        return response()->json([
            'status' => 'fail',
        ]);
    }
}
