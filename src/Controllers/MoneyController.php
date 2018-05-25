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
        // 这里假设token = account.id
        $id = $request->input('id');
        // 金额计算，这里简单假设都是整数，即没有 .00
        $money = $request->input('money');

        // begin
        app('db')->beginTransaction();

        try {
            // select * from `accounts` where `id` = ? limit 1 for update
            $account = Account::where('id', $id)->lockForUpdate()->first();

            // 存款必须大于取款
            if ((int) $account->balance >= (int) $money) {
                Account::where('id', $id)->decrement('balance', $money);
                app('db')->commit();
            } else {
                // 余额不足
                throw new Exception('余额不足');
            }

        } catch (Exception $e) {
            app('db')->rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
            ]);
        }

    }

    /**
     * 存款
     */
    public function deposit(Request $request)
    {

        $id = $request->input('id');

        $money = $request->input('money');

        // UPDATE accounts SET balance = balance + ? WHERE id = ?
        $affected_row = Account::where('id', $id)->increment('balance', $money);

        if ($affected_row > 0) {
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

        // 转移之前使用以下API获得批准
        $res = $this->isApproved();




        $from_id = $request->input('from_id');

        $to_id = $request->input('to_id');

        $money = $request->input('money');

        // 自己转帐给自己 - 不收手续费
        if ($from_id == $to_id) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        // begin
        app('db')->beginTransaction();

        // 每笔转帐，固定服务费100美元
        $fixed_service_charge = config('bank.fixed_service_charge');

        try {
            // select * from `accounts` where `id` = ? limit 1 for update
            $from_account = Account::where('id', $from_id)->lockForUpdate()->first();

            // 存款必须大于取款
            if ((int) $from_account->balance + (int) $fixed_service_charge >= (int) $money) {
                // 扣转帐费用 + 转帐金额
                Account::where('id', $from_id)->decrement('balance', (int) $money + (int) $fixed_service_charge);
                // 对方帐户添加 转帐存款
                Account::where('id', $to_id)->increment('balance', $money);
                app('db')->commit();
            } else {
                // 余额不足
                throw new Exception('余额不足');
            }

        } catch (Exception $e) {
            app('db')->rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);

    }

    protected function isApproved()
    {
        // http://handy.travel/test/success.json
    }
}
