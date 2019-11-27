<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yilianzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static  $changeUrl =true;
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
        self::$method = 'header';
        self::$reqType = 'curl';
        self::$unit=2;
        self::$payWay  = $payConf['pay_way'];

        $data = [];
        $data['merchantNo'] = $payConf['business_num'];   //商户号
        $data['nonceStr'] = mt_rand(1000, 9999);      //随机参数
        $data['paymentType']=$bank;
        $data['mchOrderNo'] = $order;         //订单号
        $data['orderTime'] = date('YmdHis');   //时间
        $data['goodsName']='ipad';
        $data['amount'] = $amount* 100;                //金额
        $data['clientIp']=self::getIp();
        $data['notifyUrl'] = $ServerUrl;              //异步回调
        $data['buyerId']=$payConf['business_num'];
        $data['buyerName']='Y';

        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getMd5Sign("{$signStr}&appkey=", $payConf['md5_private_key']);

        $post['data'] = json_encode($data);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        $post['mchOrderNo']=$data['mchOrderNo'];
        $post['amount']=$data['amount'];
        $post['queryUrl'] = $reqData['formUrl'] . "/api_gateway/pay/order/create";

        unset($reqData);
        return $post;
    }

    /**
     * @param $request
     * @return mixed   金额处理
     */
    public static function getVerifyResult($request)
    {
        $arr = $request->all();
        $res['amount']=$arr['amount']/100;
        $res['merchantOrderNo'] = $arr['merchantOrderNo'];
        return $res;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign=$data['sign'];
        unset($data['sign']);
        $signStr       = self::getSignStr($data, true, true);
        $myToken = md5($signStr . "&appkey=" . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($myToken) && $data['orderStatusCode']== 'SUCCESS'){
            return true;
        } else {
            return false;
        }
    }

}


