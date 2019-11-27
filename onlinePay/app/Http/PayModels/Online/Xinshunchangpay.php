<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use phpDocumentor\Reflection\Types\Self_;

class Xinshunchangpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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
        self::$unit = 2 ;           //单位：分；最低10元
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        $data = array(
            'appid'         =>       $payConf['business_num'],       //商户ID
            'price'         =>       $amount * 100,                  //金额
            'type'          =>       $bank,                          //支付类型
            'goodsname'     =>       'alipay',                       //订单描述
            'down_trade_no' =>       $order,                         //订单号
            'backurl'       =>       $ServerUrl,                     //异步通知地址
        );
        $signStr = self::getSignStr($data, false, true);
        $data['sign'] = md5($signStr . '&signkey=' . $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }
    public static function getQrcode($req)
    {
        $arr = json_decode($req,true);
        $returnData = [];
        if ($arr['code'] == 1) {//成功
            $returnData['qrcode'] = $arr['data']['qrcode'];
        } else { //失败
            $returnData['code'] = $arr['code'];
            $returnData['msg'] = $arr['msg'];
        }
        return $returnData;
    }
}