<?php

// Open account
$app->post('/bank/accounts', 'BankController@store');

// Close account
$app->delete('/bank/accounts/{accounts}', 'BankController@destroy');

