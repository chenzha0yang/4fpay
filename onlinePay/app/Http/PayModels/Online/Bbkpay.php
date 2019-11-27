<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bbkpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array       $reqData 接口传递的参数
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
//        self::$resType = 'other';

        $data = array(
            "fxid" => $payConf['business_num'], //商户号
            "fxddh" => $order, //商户订单号
            "fxdesc" => '124', //商品名
            "fxfee" => $amount, //支付金额 单位元
            "fxattch" => 'mytest', //附加信息
            "fxnotifyurl" => $ServerUrl, //异步回调 , 支付结果以异步为准
            "fxbackurl" => $returnUrl, //同步回调 不作为最终支付结果为准，请以异步回调为准
            "fxpay" => $bank, //支付类型 此处可选项以网站对接文档为准 微信公众号：wxgzh   微信H5网页：wxwap  微信扫码：wxsm   支付宝H5网页：zfbwap  支付宝扫码：zfbsm 等参考API
            "fxip" => self::getIp(), //支付端ip地址
            'fxbankcode'=>'',
            'fxfs'=>'',
        );

        $data["fxsign"] = md5($data["fxid"] . $data["fxddh"] . $data["fxfee"] . $data["fxnotifyurl"] . $payConf['md5_private_key']); //加密

        unset($reqData);
        return $data;
    }
    

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $fxid = $data['fxid']; //商户编号
        $fxddh = $data['fxddh']; //商户订单号
        $fxorder = $data['fxorder']; //平台订单号
        $fxdesc = $data['fxdesc']; //商品名称
        $fxfee = $data['fxfee']; //交易金额
        $fxattch = $data['fxattch']; //附加信息
        $fxstatus = $data['fxstatus']; //订单状态
        $fxtime = $data['fxtime']; //支付时间
        $fxsign = $data['fxsign']; //md5验证签名串

        $mySign = md5($fxstatus . $fxid . $fxddh . $fxfee . $payConf['md5_private_key']); //验证签名

        if ($sign == $mySign && $data['fxstatus'] == '1') {
            return true;
        } else {
            return false;
        }

    }

}