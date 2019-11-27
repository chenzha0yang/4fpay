<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qianduoduopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$reqType = 'curl';
        self::$method  = 'header';
        if ($payConf['pay_way'] == '1' || $payConf['pay_way'] == '9' || $payConf['is_app'] == '1') {
            self::$isAPP = true;
        }
        $data = [];
        if($payConf['pay_way'] == '1'){
            $data['pay_fs'] = 'b2c';//支付方式
            $data['pay_bankName'] = '1000';
            $data['pay_returnUrl'] = $ServerUrl;
        }else{
            $data['pay_fs'] = $bank;//支付方式
        }
        $data['merchantNo'] = $payConf['public_key'];//机构号
        $data['pay_orderNo'] = $order;//订单
        $data['pay_Amount'] = $amount;
        $data['pay_MerchantNo'] = $payConf['business_num'];//商户号
        $data['pay_NotifyUrl'] = $ServerUrl;
        $data['pay_ewm'] = 'No';
        $data['tranType'] = '2';
        $data['pay_ip'] = $_SERVER['HTTP_REFERER'];
        $str = $data['pay_fs']."".$data['merchantNo']."".$data['pay_orderNo']."".$data['pay_Amount']."".$data['pay_NotifyUrl']."".$data['pay_ewm'];
        $data['sign']       = self::getMd5Sign($str, $payConf['md5_private_key']);
        unset($data['merchantNo']);
        $header = array('apikey: safepay',);
        $postData['httpHeaders']     = $header;
        $postData['data']            = $data;
        $postData['pay_orderNo'] = $order;
        $postData['pay_Amount']          = $amount;
        unset($reqData);
        return $postData;
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $result = trans("backField.{$mod}");
        $json   = file_get_contents('php://input');
        $data   = Common::json_decode($json);
        if (empty($data)) {
            return [
                $result['order']  => null,
                $result['amount'] => null,
            ];
        }
        self::$data = $data;

        return $data;
    }

    /**
     * @param $type
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $sign, $payConf)
    {
        $data = self::$data;
        $sign = md5($payConf['public_key']."".$data['pay_OrderNo']."".$data['pay_Amount']."".$payConf['md5_private_key']);
        if($sign == $data['sign'] && $data['pay_Status'] == 100){
            return true;
        }else{
            return false;
        }
    }
}