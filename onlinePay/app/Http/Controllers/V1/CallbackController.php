<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\APIController;
use App\Http\Models\CallbackMsg;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayConfig;
use App\Http\Models\PayIp;
use App\Extensions\SignCheck;
use Illuminate\Http\Request;
use App\Extensions\File;

class CallbackController extends APIController
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function callback(Request $request)
    {
        $mod    = $request->callback;
        $result = trans("backField.{$mod}");
        //判断回调是否特殊处理
        //getVerifyResult 返回的数据包括订单号  金额
        //订单号 order  金额 amount
        $backSpecial = trans("backField.backSpecial");

        if (isset($backSpecial[$mod]) && $backSpecial[$mod] == 1) {
            global $app;
            $PayModel = $app->make("App\\Http\\PayModels\\Online\\$mod");
            $res = $PayModel::getVerifyResult($request, $mod);
            $order  = $res[$result['order']];
            $amount = $res[$result['amount']];

        } else {

            $order  = self::checkParameter($request, $result['order']);
            $amount = self::checkParameter($request, $result['amount']);

        }

        if (empty($order) || empty($amount)) {
            File::logResult($request->all());
            return trans("success.{$mod}");
        }

        $bankOrder = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($request->all());
            return trans("success.{$mod}");
        }

//        if ($bankOrder->money != $amount) {
//            CallbackMsg::AddCallbackMsg($request, $bankOrder, 6);
//            return trans("success.{$mod}");
//        }

        //ip验证
        if ($this->checkIp($request, $bankOrder)) {//是否开启ip验证
            return trans("success.{$mod}");
        }

        if ($bankOrder->is_status == 1 && $bankOrder->issued == 1) {
            return trans("success.{$mod}");
        }

        $payMerchant = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息

        SignCheck::$sign  = $request->all();
        SignCheck::$model = $mod;
        //根据不同的三方，获取是否需要其他参数拼接加密
        $sign = SignCheck::getPaySignAttribute($payMerchant, $result);
        if (SignCheck::SignCheck($payMerchant)) {
            $status = 1;
            if(isset($result['orderStatus'])){
                if (isset($sign[$result['orderStatus'][0]])) {
                    if ((string)$sign[$result['orderStatus'][0]] !== (string)$result['orderStatus'][1]) {
                        $status = 2;
                    }
                }
            }

            PayOrder::updateOrder($bankOrder, 'is_status', $status);
            // 自动下发, 调试时此行注释，打开下面两行
            SendCallbackController::sendCallbackByOrder($bankOrder, $status,$amount);

            // 调试使用
            // $sendLog = SendCallbackController::sendCallbackByOrder($bankOrder, $status);
            // File::logResult($sendLog);

        } else {

            CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);

        }

        unset($sign);
        unset($payMerchant);
        unset($bankOrder);

        return trans("success.{$mod}");
    }

    /**
     * ip验证
     *
     * @param Request $request
     * @param PayOrder $bankOrder
     * @return bool
     */
    private function checkIp(Request $request, PayOrder $bankOrder)
    {
        //ip验证
        $ip = self::getClientIp();
        // 调试ip测试
//        if($ip == '10.3.2.1'){
//            File::logResult($_SERVER);
//        }
        if (PayConfig::GetPayIsIpByPayType($bankOrder->pay_id)) {//是否开启ip验证
            //验证三方来源ip
            if(!PayIp::GetPayIpByIp($bankOrder->pay_id, $ip)) {
                CallbackMsg::AddCallbackMsg($request, $bankOrder, 2, $ip);
                return true;
            }
        }

        return false;
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