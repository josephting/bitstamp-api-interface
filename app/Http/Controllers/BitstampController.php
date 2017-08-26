<?php

namespace App\Http\Controllers;

use App\BitstampAPI;
use Illuminate\Http\Request;

class BitstampController extends Controller
{
    private $_key = '';
    private $_secret = '';
    private $_clientId = '';

    public $bitstamp;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->bitstamp = new BitstampAPI($request->input('key'), $request->input('signature'), $request->input('nonce'));
    }

    public function balance($currencyPair = '') {
        return response()->json($this->bitstamp->balance($currencyPair));
    }

    public function userTransactions(Request $request, $currencyPair = '') {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 100);
        $sort = $request->input('sort', 'desc');

        return response()->json($this->bitstamp->userTransactions($currencyPair, $offset, $limit, $sort));
    }

    public function openOrders($currencyPair = 'all') {
        return response()->json($this->bitstamp->openOrders($currencyPair));
    }

    public function orderStatus($id) {
        return response()->json($this->bitstamp->orderStatus($id));
    }

    public function cancelOrder($id) {
        return response()->json($this->bitstamp->cancelOrder($id));
    }

    public function cancelAllOrders() {
        return response()->json($this->bitstamp->cancelAllOrders());
    }

    public function buyLimit(Request $request, $currencyPair = 'btcusd') {
        $amount = $request->input('amount');
        $price = $request->input('price');
        $limitPrice = $request->input('limit_price', 0);
        $dailyOrder = $request->input('daily_order', 'True');

        if (is_null($amount) || is_null($price)) {
            return response()->json(['error' => true, 'message' => 'Amount and price must be set']);
        }

        return response()->json($this->bitstamp->buyLimit($currencyPair, $amount, $price, $limitPrice, $dailyOrder));
    }

    public function sellLimit(Request $request, $currencyPair = 'btcusd') {
        $amount = $request->input('amount');
        $price = $request->input('price');
        $limitPrice = $request->input('limit_price', 0);
        $dailyOrder = $request->input('daily_order', 'True');

        if (is_null($amount) || is_null($price)) {
            return response()->json(['error' => true, 'message' => 'Amount and price must be set']);
        }

        return response()->json($this->bitstamp->sellLimit($currencyPair, $amount, $price, $limitPrice, $dailyOrder));
    }
}
