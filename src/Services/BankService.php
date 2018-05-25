<?php

namespace TinkLabs\Bank\Services;

use Exception;
use TinkLabs\Bank\Exceptions\BalanceException;
use TinkLabs\Bank\Models\Account;
use TinkLabs\Bank\Models\TransferLog;
use TinkLabs\Bank\Utils\Snowflake;

/**
 * 银行服务类
 */
class BankService
{

    /**
     * Get current balance
     */
    public function getCurrentBalance($uid, $lockForUpdate = false)
    {
        if ($lockForUpdate) {
            // select * from `accounts` where `uid` = ? limit 1 for update
            $account = Account::where('uid', $uid)->lockForUpdate()->first();
        } else {
            $account = Account::where('uid', $uid)->first();
        }

        if (empty($account)) {
            return false;
        }

        return $account->balance;
    }

    /**
     * 取款
     *
     * @return bool true|false
     */
    public function withdraw($uid, $money)
    {
        $money = (int) $money;
        // begin
        app('db')->beginTransaction();

        try {

            // 存款必须大于取款
            if ($this->getCurrentBalance($uid, true) >= $money) {
                Account::where('uid', $uid)->decrement('balance', $money);
                app('db')->commit();

                return true;
            } else {
                // 余额不足
                throw new BalanceException('余额不足');
            }

        } catch (BalanceException $e) {
            app('db')->rollBack();
            throw new BalanceException($e->getMessage());
        } catch (Exception $e) {
            app('db')->rollBack();

            throw new Exception('withdraw rollBack');
        }

        return false;
    }

    /**
     * 存款
     */
    public function deposit($uid, $money)
    {
        $money = (int) $money;

        // UPDATE accounts SET balance = balance + ? WHERE uid = ?
        $affected_row = Account::where('uid', $uid)->increment('balance', $money);

        if ($affected_row > 0) {
            return true;
        }

        return false;
    }

    /**
     * 转帐 (每日限额 $10000)
     */
    public function transfer($from_uid, $to_uid, $money)
    {
        $money = (int) $money;
        // 每笔转帐，固定服务费100美元
        $fixed_service_charge = config('bank.fixed_service_charge');

        // 转帐之前使用以下API获得批准
        if (!$this->isApproved()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Disapproval',
            ]);
        }

        // 自己转帐给自己 - 不收手续费
        if ($from_uid == $to_uid) {
            return true;
        }



        app('db')->beginTransaction();

        try {
            // 锁住当前余额，不允许过程中改动
            $from_balance = $this->getCurrentBalance($from_uid, true);
            // 获取当天已转额度
            $totalDailyTransfer = $this->getTotalDailyTransfer($from_uid);
            $daily_transfer_limit = config('bank.daily_transfer_limit');

            // 超出限额
            if ($totalDailyTransfer + $money > $daily_transfer_limit) {
                throw new BalanceException('超出限额');
            }

            // 存款必须大于取款+手续费
            if ($from_balance >= $money + $fixed_service_charge) {
                // 扣转帐费用 + 转帐金额
                Account::where('uid', $from_uid)->decrement('balance', $money + $fixed_service_charge);
                // 对方帐户添加 转帐存款
                Account::where('uid', $to_uid)->increment('balance', $money);

                // 写转帐日志
                TransferLog::create([
                    'id' => Snowflake::generateId(),
                    'uid' => $from_uid,
                    'money' => $money,
                    'message' => $from_uid. ' transfer'
                ]);
                app('db')->commit();
                return true;
            } else {
                // 余额不足
                throw new BalanceException('余额不足');
            }

        } catch (BalanceException $e) {
            app('db')->rollBack();
            throw new BalanceException($e->getMessage());
        } catch (Exception $e) {
            app('db')->rollBack();
            throw new Exception('transfer rollBack');
        }

        return false;
    }

    /**
     *  转帐之前使用以下API获得批准
     */
    protected function isApproved()
    {
        return true;
        $url = 'http://handy.travel/test/success.json';
        $timeout = 60;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
        //函数中加入下面这条语句  解决遇到重定向页面
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if (!empty($result->status) && $result->status == 'success') {
            return true;
        }

        return false;
    }

    /**
     * Transfer money (daily transfer limit of $10000 per account)
     * 获取用户当天已转帐金额总和
     */
    public function getTotalDailyTransfer($uid)
    {
        $total = TransferLog::where('uid', $uid)->sum('money');

        return $total;
    }
}
