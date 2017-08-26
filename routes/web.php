<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api'], function () use ($app) {
    $app->post('balance[/{currencyPair}]', ['uses' => 'BitstampController@balance']);
    $app->post('user_transactions[/{currencyPair}]', ['uses' => 'BitstampController@userTransactions']);
    $app->post('open_orders[/{currencyPair}]', ['uses' => 'BitstampController@openOrders']);
    $app->post('order_status/{id}', ['uses' => 'BitstampController@orderStatus']);
    $app->post('cancel_order/{id}', ['uses' => 'BitstampController@cancelOrder']);
    $app->post('cancel_all_orders', ['uses' => 'BitstampController@cancelAllOrders']);
    $app->post('buy[/{currencyPair}]', ['uses' => 'BitstampController@buyLimit']);
    $app->post('sell[/{currencyPair}]', ['uses' => 'BitstampController@sellLimit']);
});
