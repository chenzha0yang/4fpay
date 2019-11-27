<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Pinbapay extends ApiModel
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
        self::$method = 'get';
        $data = array(
            'parter'        =>      $reqData['businessNum'],        //商户ID
            'type'          =>      $bank,                          //支付类型
            'value'         =>      $amount,                        //支付金额
            'orderid'       =>      $order,                         //订单号
            'callbackurl'   =>      $ServerUrl,                     //异步通知地址
        );
        $signStr = '';
        foreach ($data as $k => $v) {
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr = substr($signStr,0,strlen($signStr) -1) . $payConf['md5_private_key'];
        $data['sign'] = md5($signStr);
        $data['payerIp'] = '';                                      //支付用户IP
        $data['attach'] = '';                                       //备注消息
        unset($reqData);
        return $data;
    }
}