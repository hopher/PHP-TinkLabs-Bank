<?php

namespace TinkLabs\Bank\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use TinkLabs\Bank\Models\Account;

class BalanceController extends Controller
{

    /**
     * Get current balance
     */
    public function show(Request $request)
    {
        // 这里假设token = account.id
        $id = $request->input('id');
        $account = Account::find('id', $id);

        if (isset($account)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'balance' => $account->balance,
                ],
            ]);
        }

        return response()->json([
            'status' => 'fail',
        ]);
    }
}
