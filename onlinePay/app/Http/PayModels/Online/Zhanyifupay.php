<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhanyifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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

        //TODO: do something

        //获取时间戳
        date_default_timezone_set("PRC");
        //将时间戳转成北京时间日期
        $data = [];
        $data['body']            = 'buyfood';
        $data['datetime']        = date("Y-m-d H:i:s", time());
        $data['merchant_num']    = $payConf['business_num'];   //商户号
        $data['notify_url']      = $ServerUrl;
        $data['order_num']       = $order;
        $data['pay_money']       = $amount;
        $data['return_url']      = $returnUrl;
        $data['title']           = 'iphone8';
        $signStr = self::getSignStr($data, false);
        $md5sign = strtoupper(self::getMd5Sign(urlencode("{$signStr}".'&ukey='.$payConf['md5_private_key']), ''));

        $data['notify_url'] =$ServerUrl;
        $data['pay_type'] =$bank;
        $data['v'] = 'pc';
        $data['sign'] = $md5sign;
        return $data;
    }

    /**
     * @param $mod
     * @param $res
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $res, $payConf)
    {
        $merchantNum = $res["merchant_num"];
        $orderStatus = $res["order_status"];       //0 支付失败 1 支付成功
        $merRmk = $res["mer_rmk"];
        $payType = $res["pay_type"];               //1微信扫码 2支付宝扫码
        $dateTime = $res["datetime"];
        $privateKey = $payConf['md5_private_key'];     //密钥
        $orderNum = $res["order_num"];           //商户订单号
        $amount = $res["pay_money"];             //充值金额

        $sign = "";
        $sign = $sign."datetime=".$dateTime."&";

        if(!empty($merRmk)){
            $sign = $sign."mer_rmk=".$merRmk."&";
        }
        $sign = $sign."merchant_num=".$merchantNum."&";
        $sign = $sign."order_num=".$orderNum."&";
        $sign = $sign."order_status=".$orderStatus."&";
        $sign = $sign."pay_money=".$amount."&";
        $sign = $sign."pay_type=".$payType."&";
        $sign = $sign."ukey=".$privateKey;
        $sign = urlencode($sign);
        $md5sign = strtoupper(md5($sign));

        if($res['sign'] == $md5sign){
            if($orderStatus == 1){
                return true;
            }
        }else{
            return false;
        }
    }
}