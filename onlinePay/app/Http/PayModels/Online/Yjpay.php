<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yjpay extends ApiModel
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
        //TODO: do something

        $data              = array();
        $data['merId']     = $payConf['business_num'];//商户编号
        $data['ordId']     = $order;//订单编号
        $data['amount']    = $amount;//订单金额，以分为单位
        $data['payType']   = $bank;//支付类型    
        $signStr           = self::getSignStr($data, true, true); //true1 为空不参与签名，true2排序
        $data['sign']      = self::getMd5Sign($signStr, $payConf['md5_private_key']); 
        $data['body']      = 'body';//支付结果通知商户地址
        $data['synUrl']    = $ServerUrl;//异步通知地址
        $data['selfParam'] = $order;  //异步通知返回给商户

        self::$payWay  = $payConf['pay_way'];
        self::$reqType = 'curl';
        self::$unit    = 2;
        self::$resType = 'other';
        self::$httpBuildQuery = true;
        unset($reqData);
        return $data;
    }

    /**
     * @param $resultData
     * @return array
     */
    public static function getQrCode($resultData){
        $result = json_decode($resultData,true);
        $res = [];
        if($result['responseCode'] == '0'){
            $res['ordId']   = $result['data']['ordId'];
            $res['amount']  = $result['data']['transAmount'];
            if(isset($result['qrCodeUrl'])){
                $res['qrCodeUrl'] = $result['qrCodeUrl'];
            }else{
                $res['codeurl'] = $result['data']['codeurl'];
            }
        }else{
            $res['return_code'] = $result['responseCode'];
            $res['return_msg'] = $result['responseMsg'];
        }
        return $res;
    }
}
