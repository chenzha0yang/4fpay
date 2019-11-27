<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Extensions\RedisConPool;

class CallbackMsg extends Model
{
    protected $table = 'pay_callback_msg';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 写入回调失败信息
     *
     * @param Request $request
     * @param PayOrder $order 订单信息
     * @param int $error 错误码 1 验签失败 2 IP黑名单 3 入款失败 4 回调地址异常  5 回调超时 6 金额异常
     * @param string $ip 回调来源IP
     * @internal param Request $requestData 接收参数
     */
    public static function AddCallbackMsg(Request $request, $order = null, $error = 1, $ip = '')
    {
        $requestData = $request->all();
        if (empty($requestData) || empty($order)) {//未接收到数据
            $map['order_number'] = '0';
            $map['callback_msg'] = "未接收到REQUEST数据";
        } else {
            $map['order_number'] = $order->order_number;
            $map['pay_id']       = $order->pay_id;
            $map['pay_way']      = $order->pay_way;
            $map['client_id']    = $order->client_user_id;
            if (is_array($requestData)) {
                $requestData = json_encode($requestData);
            }
            $map['callback_msg'] = $requestData;
        }
        $map['error_code']   = $error;
        $map['callback_url'] = 'https://' . $_SERVER['HTTP_HOST'] . $request->getPathInfo();
        if ($ip) {
            $map['callback_ip'] = $ip;
        }

        self::create($map);
    }

    /**
     * 出款记录写入回调日志
     *
     * @param      $requestData 三方返回信息
     * @param null $order 注单信息
     * @param      $error 状态 4 请求 5 查询
     * @param      $callUrl 回调地址
     */
    public static function OutCallbackMsg($requestData, $order = null, $error, $callUrl)
    {
        if (empty($requestData) || empty($order)) {//未接收到数据
            $map['order_number'] = '0';
            $map['callback_msg'] = "未接收到REQUEST数据";
        }
        if ($error == 4) {
            $map['order_number'] = $order->order_number;
            $map['pay_id']       = $order->pay_id;
            $map['pay_way']      = '0';
            $map['client_id']    = $order->client_user_id;
            if (is_array($requestData)) {
                $requestData = json_encode($requestData);
            }
            $map['callback_msg'] = $requestData;
            $map['error_code']   = $error;
            $map['callback_url'] = $callUrl;
            self::create($map);
        } else {
            //设置查询结果key,防止多次查询结果重复更新数据
            $mgKey = 'qrmsg'.'_'.$order->order_number.'_'.$order->agent_id;
            $getmsgkey = RedisConPool::get($mgKey);
            if (!empty($getmsgkey) && $getmsgkey <> $requestData) {
                self::where('order_number', $order->order_number)->where('error_code', $error)->update(['callback_msg' => $requestData, 'error_code' => $error]);
                RedisConPool::del($mgKey);
            } else {
                RedisConPool::set($mgKey, $requestData);
            }
        }
    }

    public static function addDebugLogs($log,$callback_url = 'no')
    {
        $json = json_encode($log);
        $map['order_number'] = 'debugLog123456789';
        $map['callback_msg'] = $json;
        $map['pay_id']       = 1;
        $map['pay_way']      = 3;
        $map['client_id']    = 1;
        $map['error_code']   = 2;
        $map['callback_url'] = $callback_url;
        self::create($map);
    }

}