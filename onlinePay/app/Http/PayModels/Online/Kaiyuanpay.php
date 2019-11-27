<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kaiyuanpay extends ApiModel
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

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit = 2;

        $data['app_id'] = $payConf['business_num'];       //商户号
        $data['price'] = $amount * 100;        //金额
        $data['goods_name'] = '1';      //平台商品名称
        $data['order_no'] = $order;                  //平台商品订单号
        $data['notify_url'] =$ServerUrl ;             //服务端通知
        $data['return_url'] = $returnUrl;           //同步通知
        $data['pay_type'] =$bank ;                   //支付方式
        $data['format'] ='json' ;                   //格式

        ksort($data ); #key顺序排序

        $str='';

        foreach($data as $k=>$v ) {
            $str.= ($k).'='.($v).'&';
        }

        $str.= 'app_secret='.$payConf['md5_private_key']; #将秘钥加进来

        $data['sign']= md5( strtolower( $str)); #先转为为小写 md5得到签名
        return $data;
    }

    /**
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $result = json_decode($res, true);
        if ($result['error'] == '0') {
            $result['url'] = $result['data']['pay_data']['url'];
        }
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['price'])) {
            $arr['price'] = $arr['price'] / 100;
        }
        return $arr;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        ksort($data ); #key顺序排序

        $str='';

        foreach($data as $k=>$v ) {
            $str.= ($k).'='.($v).'&';
        }

        $str.= 'app_secret='.$payConf['md5_private_key']; #将秘钥加进来

        $mySign= md5( strtolower( $str));   #先转为为小写 md5得到签名

        if(strtolower($sign) == $mySign){
            return true;
        }else{
            return false;
        }
    }

}