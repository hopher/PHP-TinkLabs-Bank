<?php

// Open account
$app->post('/bank/accounts', 'BankController@store');

// Close account
$app->delete('/bank/accounts/{accounts}', 'BankController@destroy');

// Get current balance
$app->get('/bank/balance', 'BalanceController@show');

// Withdraw money   取款
$app->post('/bank/money/withdraw', 'BankController@destroy');

// Deposit money    存款
$app->post('/bank/money', 'BankController@destroy');

// Transfer money (daily transfer limit of $10000 per account)
// 转帐 (每日限额 $10000)
$app->get('/bank/balance', 'BankController@destroy');

// 
