<?php

// Open account
$app->post('/bank/accounts', 'AccountController@store');

// Close account
$app->delete('/bank/accounts/{accounts}', 'AccountController@destroy');

// Get current balance
$app->get('/bank/balances/show', 'BalanceController@show');

// Withdraw money   取款
$app->post('/bank/money/withdraw', 'MoneyController@withdraw');

// Deposit money    存款
$app->post('/bank/money/deposit', 'MoneyController@deposit');

// Transfer money (daily transfer limit of $10000 per account)
// 转帐 (每日限额 $10000)
$app->post('/bank/money/transfer', 'MoneyController@transfer');