<?php

namespace App;

use GuzzleHttp\Client;

class BitstampAPI
{
    private $_key;
    private $_signature;
    private $_nonce;

    public function __construct($key, $signature, $nonce) {
        $this->_key = $key;
        $this->_signature = $signature;
        $this->_nonce = $nonce;
    }

    public function balance($currencyPair = '') {
        return $this->_doRequest('v2/balance'.($currencyPair !== '' ? '/'.$currencyPair : ''));
    }

    public function userTransactions($currencyPair = '', $offset = 0, $limit = 100, $sort = 'desc') {
        return $this->_doRequest('v2/user_transactions'.($currencyPair !== '' ? '/'.$currencyPair : ''), [
            'offset' => $offset,
            'limit' => min($limit, 1000),
            'sort' => strtolower($sort) == 'asc' ? 'asc' : 'desc'
        ]);
    }

    public function openOrders($currencyPair = 'all') {
        return $this->_doRequest('v2/open_orders/'.$currencyPair);
    }

    public function orderStatus($id) {
        return $this->_doRequest('order_status', [
            'id' => $id
        ]);
    }

    public function cancelOrder($id) {
        return $this->_doRequest('v2/cancel_order', [
            'id' => $id
        ]);
    }

    public function cancelAllOrders() {
        return $this->_doRequest('cancel_all_orders');
    }

    public function buyLimit($currencyPair = 'btcusd', $amount, $price, $limitPrice = 0, $dailyOrder = false) {
        $params = [
            'amount' => $amount,
            'price' => $price
        ];
        if ($limitPrice > 0) {
            $params['limit_price'] = $limitPrice;
        }
        if ($dailyOrder !== false) {
            $params['daily_order'] = 'True';
        }
        return $this->_doRequest('v2/buy/'.$currencyPair, $params);
    }

    public function sellLimit($currencyPair = 'btcusd', $amount, $price, $limitPrice = 0, $dailyOrder = false) {
        $params = [
            'amount' => $amount,
            'price' => $price
        ];
        if ($limitPrice > 0) {
            $params['limit_price'] = $limitPrice;
        }
        if ($dailyOrder !== false) {
            $params['daily_order'] = 'True';
        }
        return $this->_doRequest('v2/sell/'.$currencyPair, $params);
    }

    private function _doRequest($action, array $params = [], $method = 'post') {
        $time = explode(" ", microtime());
        $nonce = $time[1].substr($time[0], 2, 6);
        $params['nonce'] = $this->_nonce;
        $params['key'] = $this->_key;
        $params['signature'] = $this->_signature;
        $request = http_build_query($params, '', '&');

        $headers = array();
        $client = new Client();
        $response = $client->request(strtoupper($method), 'https://www.bitstamp.net/api/'.$action.'/', [
            'form_params' => $params
        ]);
        // $response = $client->request(strtoupper($method), 'https://requestb.in/uoz1niuo', [
        //     'form_params' => $params
        // ]);

        $data = json_decode($response->getBody()->getContents());

        if (!$data) {
            return array('error' => 2, 'message' => 'Invalid data received, please make sure connection is working and requested API exists');
        }

        return $data;
    }
}
