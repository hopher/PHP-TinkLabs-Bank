<?php

namespace TinkLabs\Bank\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use TinkLabs\Bank\Models\Account;

class AccountController extends Controller
{
    /**
     * Open account
     */
    public function store(Request $request)
    {

        Account::insert([
            'user_name' => 'xxxx',
            'card_number' => 'xxxx'
        ]);
        
        if (0) {
            return response()->json([
                'status' => 'fail'
            ]);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Close account
     */
    public function destroy(Request $request, $id)
    {
        Account::where([
            'id' => $id,
        ])->delete();        

        return response()->json([
            'status' => 'success'
        ]);        
    } 

}
