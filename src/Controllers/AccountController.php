<?php

namespace TinkLabs\Bank\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use TinkLabs\Bank\Models\Account;
use TinkLabs\Bank\Utils\Snowflake;

class AccountController extends Controller
{
    /**
     * Open account
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:6|max:8|regex:/^[a-zA-Z]/', // 这里有效字符根据业务需求来定
            'password_confirm' => 'required|same:password', // 密码确认
        ]);

        $password = $request->input('password');
        $username = $request->input('username');
        $salt = Snowflake::randomString();

        try {
            $account = [
                'id' => Snowflake::generateId(), // 产生分布式UID
                'username' => $username,
                'password' => md5($password . $salt),
                'salt' => $salt,
            ];

            Account::insert($account);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $account['id'],
                'username' => $account['username'],
            ],
        ]);
    }

    /**
     * Close account
     *
     * @param   int     $it 分布式UUID
     */
    public function destroy(Request $request, $id)
    {
        $affected_row = Account::where([
            'id' => $id,
        ])->delete();

        if ($affected_row > 0) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'fail',
        ]);
    }

}
