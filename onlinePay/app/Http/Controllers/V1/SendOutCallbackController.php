<?php

namespace App\Http\Controllers\V1;

use App\Extensions\Curl;
use App\Http\Models\Client;
use App\Http\Models\OutOrder;
use App\Http\Models\PayCallbackUrl;
use App\Http\Models\OutMerchant;
use App\Http\Models\SendCallbackLog;

class SendOutCallbackController
{
    private static $callRes = [
        "status"  => false,
        "message" => '',
    ];

    /**
     * 订单自动下发
     *
     * @param $orderData
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendCallbackByOrder($orderData, $status)
    {
        if ($orderData) {

            $client = Client::where('user_id', $orderData->client_user_id)->first();

            $callbackUrl = $orderData->callback_url;

            if ($orderData) {
                if ($callbackUrl) {
                    Curl::$url = $callbackUrl;
                    if ($status == 1 || $orderData->is_status == 1) {
                        $data = [
                            "status" => true,
                            "code" => 200,
                            "message" => trans('success.succeeded'),
                            "sendTime" => date('Y-m-d H:i:s', time()),
                            "data" => json_encode(self::paraDisguise($orderData, $client)),
                        ];
                    } else {
                        $data = [
                            "status" => false,
                            "code" => 200,
                            "message" => trans('error.failed'),
                            "sendTime" => date('Y-m-d H:i:s', time()),
                            "data" => json_encode(['errorMsg' => trans('error.orderSure')[2]]),
                        ];
                    }

                    Curl::$request = $data;

                    $callResponse = Curl::sendRequest();

                    // 下发日志
                    SendCallbackLog::createLog($orderData->order_number,$callResponse, $data, $callbackUrl, 1, 2);

                    $bool = OutOrder::editOrderIssued($orderData, $callResponse);

                    if (!$bool) {
                        self::setCallMessage(trans('error.issued')[2]);
                    }

                } else {
                    self::setCallMessage(trans('error.sendOrderError')[0]);
                }
            } else {
                self::setCallMessage(trans('error.sendOrderError')[1]);
            }

        } else {
            self::setCallMessage(trans('error.sendOrderError')[2]);
        }

        return response()->json(self::$callRes);
    }


    /**
     * 设置callRes属性
     *
     * @param $message
     */
    public static function setCallMessage($message)
    {
        if (is_array($message)) {
            self::$callRes = $message;
        } else {
            self::$callRes["message"] = $message;
        }
    }

    /**
     * 下发信息参数伪装
     *
     * @param $orderData
     * @param $client
     * @return array
     */
    public static function paraDisguise($orderData, $client)
    {
        $data                 = [];
        $data['order']        = (string)$orderData->order_number;    //订单号
        $data['agentId']      = (string)$orderData->agent_id;        //代理线
        $data['agentNum']     = (string)$orderData->agent_num;       //子代理线
        $data['amount']       = (string)$orderData->money;           //出款金额
        $data['status']       = (int)$orderData->is_status;          //状态
        $data['businessNum']  = (string)$orderData->business_num;    //商户号
        $data['clientSecret'] = $client->secret;
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $val) {
            if (!is_null($val) && $val !== '') {
                $signStr .= "&&&{$key}=#{$val}#&&&";
            }
        }
        unset($data['clientSecret']);
        $data['sign'] = md5(md5($signStr));
        return $data;
    }
}