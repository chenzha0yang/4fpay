<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fengyepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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

        $data                 = array();
        if ($payConf['pay_way'] == 1){
            $data['bank_code'] = $bank; //银行编码
            $data['pay_type'] = '000'; //银行编码
        } else {
            $data['pay_type']  = $bank; //支付类型
        }
        $data['mch_id']       = $payConf['business_num']; //商户号
        $data['notify_url']   = $ServerUrl; //后台通知地址
        $data['mch_order_no'] = $order; //订单号
        $data['trade_amount']   = $amount; //交易金额
        $data['order_date']      = date('Y-m-d H:i:s'); //订单时间
        $data['goods_name']     = '2345';   //商品名称

        $signStr      = self::getSignStr($data, true,true);
        if ($payConf['pay_way'] == 1){
            $data['page_url'] = $returnUrl; //前台通知地址
        }

        $data['sign'] = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $data['sign_type'] = 'MD5';    //签名方式
        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data,$payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['signType']);
        $signStr      = self::getSignStr($data, true,true);
        $signTrue = (self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }

}