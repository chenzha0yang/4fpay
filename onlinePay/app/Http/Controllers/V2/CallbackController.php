<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\APIController;
use App\Http\Models\CallbackMsg;
use App\Http\Models\PayOrder;
use App\Http\Models\PayConfig;
use App\Http\Models\PayIp;
use Illuminate\Http\Request;

class CallbackController extends APIController
{

    // 三方对应模型
    private $PayModel = null;

    public static $amount;

    /**
     * @param Request $request
     */
    public function callback(Request $request)
    {
        $mod = $request->callback;
        global $app;
        $this->PayModel = $app->make("App\\Http\\PayModels\\Online\\$mod");

        if ($this->PayModel::callback($request) && self::getStatus($request)){
            PayOrder::updateOrder(($this->PayModel)::$order, 'is_status', 1);
            SendCallbackController::sendCallbackByOrder(($this->PayModel)::$order, 1,($this->PayModel)::$amount);
        }
    }

    /**
     * @param $request
     * @return bool
     */
    private function getStatus($request)
    {
        $order = ($this->PayModel)::$order;
        $ip = self::getClientIp();
        if (PayConfig::GetPayIsIpByPayType($order->pay_id)) {//是否开启ip验证
            //验证三方来源ip
            if(!PayIp::GetPayIpByIp($order->pay_id, $ip)) {
                CallbackMsg::AddCallbackMsg($request, $order, 2, $ip);
                return false;
            }
        }
        if ($order->is_status == 1 && $order->issued == 1) return false;
        return true;
    }

    /**
     * 获取客户端IP地址
     * @return array|false|mixed|string
     */
    protected static function getClientIp()
    {
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)){
            if(isset($_SERVER['HTTP_CDN_SRC_IP']) && !empty($_SERVER['HTTP_CDN_SRC_IP']) && strcasecmp($_SERVER['HTTP_CDN_SRC_IP'], $unknown)){
                $realip = $_SERVER['HTTP_CDN_SRC_IP'];
            }else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($arr as $ip){
                    $ip = trim($ip);
                    if ($ip != 'unknown'){
                        $realip = $ip;
                        break;
                    }
                }
            }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
                $realip = $_SERVER['REMOTE_ADDR'];
            }else{
                $realip = $unknown;
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
                $realip = getenv("HTTP_CLIENT_IP");
            }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
                $realip = getenv("REMOTE_ADDR");
            }else{
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return $realip;
    }
}