<?php

namespace TinkLabs\Bank\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use TinkLabs\Bank\Models\Account;

class MoneyController extends Controller
{

    /**
     * 取款 -- 排它锁应用
     */
    public function withdraw(Request $request)
    {
        // 这里假设token = account.uid
        $uid = $request->input('uid');
        // 金额计算，这里简单假设都是整数，即没有 .00
        $money = $request->input('money');

        $withdraw = app('bank')->withdraw($uid, $money);

        if ($withdraw) {
            return response()->json([
                'status' => 'success',
            ]);   
        }

        return response()->json([
            'status' => 'fail',
        ]);

    }

    /**
     * 存款
     */
    public function deposit(Request $request)
    {

        $uid = $request->input('uid');

        $money = $request->input('money');

        $deposit = app('bank')->deposit($uid, $money);

        if ($deposit) {
            return response()->json([
                'status' => 'success',
            ]);   
        }

        return response()->json([
            'status' => 'fail',
        ]);

    }

    /**
     * 转帐 (每日限额 $10000)
     *
     */
    public function transfer(Request $request)
    {

        $this->validate($request, [
            'from_uid' => 'required',
            'to_uid' => 'required',
            'money' => 'required|Integer',
        ]);

        $from_uid = $request->input('from_uid');
        $to_uid = $request->input('to_uid');
        $money = $request->input('money');

        $transfer = app('bank')->transfer($from_uid, $to_uid, $money);

        if ($transfer) {
            return response()->json([
                'status' => 'success',
            ]);            
        }

        return response()->json([
            'status' => 'fail',
        ]);

    }

}
