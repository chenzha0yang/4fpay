<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Tongbaoyhpay extends ApiModel
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

        //TODO: do something
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }
        $data = [
            'pay_notifyurl'  => $ServerUrl,
            'pay_merberid'   => $payConf['business_num'],    //商户号
            'pay_orderno'    => $order,                      //订单号
            'pay_amount'     => $amount,                 //金额
            'pay_ordertime'  => date('Y-m-d H:i:s'),
        ];
        if($payConf['pay_way'] == '1'){ //网银
            $data['pay_biztype']  = 1;
            $data['pay_bankcode'] = $bank;
        }else{
            $data['pay_biztype']  = $bank;
        }

        $signStr = self::getSignStr($data, true,true);
        $data['sign'] = self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

}