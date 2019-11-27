<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinjinapay extends ApiModel
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

        $data = [];
        $data = array(
            "version" => 'V2.0',                    //版本号
            "tranType" => '05',                     //交易类型
            "merId" => $payConf['business_num'],    //商户号
            "payProduct"=> $bank,                   //指定支付产品
            "orderNo" => $order,                    //订单流水号
            "transAmt" => $amount,                  //交易金额
            "notifyUrl" => $ServerUrl,              //异步通知接口
            "returnUrl" => $returnUrl,              //同步返回地址
            "remarks" => 'zhif'                     //备注
        );
        $signStr = self::getSignStr($data, true, true);
        $data['signature'] = self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $data = json_decode($data, true);
        $temp='';
        ksort($data);     //对数组进行排序
        foreach ( $data as $v => $value ) {
            if ( $v != 'signature'&& $value != null ) {
                $temp = $temp.$v."=".$value."&";
            }
        }
        $sign = strtoupper(self::getMd5Sign("{$temp}&", $payConf['md5_private_key']));

        if(strtoupper($sign) == strtoupper($data['signature'])){
            if($data['status'] == 'S'){
               return true;
            }
        } else {
            return false;
        }
    }
}