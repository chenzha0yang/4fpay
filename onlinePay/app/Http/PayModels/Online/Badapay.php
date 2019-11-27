<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Badapay extends ApiModel
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
        $data = [
            'version'     => '3.0',                    //版本号
            'method'      => 'pay',                    //接口名称
            'partner'     => $payConf['business_num'], //商户ID
            'banktype'    => $bank,                    //银行类型
            'paymoney'    => $amount,                  //金额
            'ordernumber' => $order,                   //订单号
            'timestamp'   => self::getMillisecond(),   //请求时间戳 -- 精确到毫秒
            'callbackurl' => $ServerUrl,               //异步通知地址
        ];
        $signStr      = self::getSignStr($data, false);
        $data['sign'] = self::getMd5Sign($signStr, $payConf['md5_private_key']); //MD5签名
        $data['requesttype'] = 'pro';                          //请求类型
        $data['hrefbackurl'] = $returnUrl;                  //同步通知地址
        $data['goodsname']   = 'goods';                      //商品名称
        $data['memberId']    = '';                           //会员号
        $data['attach']      = '';                           //备注信息

        unset($reqData);
        return $data;
    }

    /**
     * 获取时间戳到毫秒
     * @return bool|string
     */
    public static function getMillisecond(){
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime,0,13);
    }
}