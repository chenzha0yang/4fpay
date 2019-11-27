<?php

namespace App\Http\Controllers\V2;

use App\Extensions\Curl;
use App\Http\Models\Client;
use App\Http\Models\PayCallbackUrl;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use App\Http\Models\SendCallbackLog;
use App\Extensions\RedisConPool;

class SendCallbackController
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
     * @param $amount
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendCallbackByOrder($orderData, $status,$amount)
    {
        if ($orderData) {
            $client = Client::where('user_id', $orderData->client_user_id)->first();
            if (empty($callbackUrl = $orderData->callback_url)) {
                $merchant    = PayMerchant::find($orderData->merchant_id);
                $callbackUrl = PayCallbackUrl::getCallbackUrlByAgent([
                    'agentId'    => $merchant->agent_id,
                    'agentNum'   => $merchant->agent_num,
                    'clientName' => $client->client_name,
                    'clientId'   => $client->id,
                ]);
            }
            if ($orderData) {
                if ($callbackUrl) {
                    $redisKey = "{$orderData->client_user_id}_{$orderData->order_number}_finish";
                    $finish = RedisConPool::get($redisKey);
                    if($finish == 1){
                        self::setCallMessage(trans('error.sendOrderError')[3]);
                    } else {
                        RedisConPool::setex($redisKey,60,1);
                        Curl::$url = $callbackUrl;
                        if ($status === 1 && $orderData->is_status === 1 && $orderData->issued !== 1) {
                            $data = [
                                "status"   => true,
                                "code"     => 200,
                                "message"  => trans('success.succeeded'),
                                "sendTime" => date('Y-m-d H:i:s', time()),
                                "data"     => json_encode(self::paraDisguise($orderData, $client,$amount)),
                            ];
                        } else {
                            $data = [
                                "status"   => false,
                                "code"     => 200,
                                "message"  => trans('error.failed'),
                                "sendTime" => date('Y-m-d H:i:s', time()),
                                "data"     => json_encode(['errorMsg' => trans('error.orderSure')[2]]),
                            ];
                        }

                        Curl::$request = $data;

                        $callResponse = Curl::sendRequest();

                        // 下发日志
                        SendCallbackLog::createLog($orderData->order_number, $callResponse, $data, $callbackUrl, 1, 1);

                        $bool = self::changeOrderIssued($orderData, $callResponse === 'SUCCESS' ? 1 : 2);

                        if (!$bool) {
                            self::setCallMessage(trans('error.issued')[2]);
                        }
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
    private static function setCallMessage($message)
    {
        if (is_array($message)) {
            self::$callRes = $message;
        } else {
            self::$callRes["message"] = $message;
        }
    }

    /**
     * 修改订单下发状态
     *
     * @param PayOrder $payOrder
     * @param int $issued
     * @return bool
     */
    private static function changeOrderIssued(PayOrder $payOrder, int $issued)
    {
        self::setCallMessage([
            'status'  => $issued === 1,
            'message' => trans('error.issued')[$issued],
        ]);

        return PayOrder::updateOrder($payOrder, 'issued', $issued);
    }

    /**
     * 下发信息参数伪装
     *
     * @param $orderData
     * @param $client
     * @param $amount 实际金额
     * @return array
     */
    private static function paraDisguise($orderData, $client,$amount)
    {
        $data                 = [];
        $data['order']        = (string)$orderData->order_number;    //订单号
        $data['agentId']      = (string)$orderData->agent_id;        //代理线
        $data['agentNum']     = (string)$orderData->agent_num;       //子代理线
        $data['amount']       = (string)$amount;           //支付金额
        $data['status']       = (int)$orderData->is_status;       //状态
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