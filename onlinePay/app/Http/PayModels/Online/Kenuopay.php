<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Kenuopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $param = false;

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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$unit = 2;

        $data = [
            'apps_id'       => $payConf['message1'],        //应用ID
            'out_trade_no'  => $order,                      //订单号
            'mer_id'        => $payConf['business_num'],    //商户号
            'total_fee'     => $amount*100,                 //金额
            'subject'       => 'goods',                     //商品标题
            'body'          => '',
            'notify_url'    => $ServerUrl,
            'return_url'    => $returnUrl,
            'show_url'      => '',
            'user_id'       => '',
            'extra'         => '',
        ];

        $signStr = self::getSignStr($data, true,true);
        $res = openssl_pkey_get_private($payConf['rsa_private_key']);
        openssl_sign($signStr, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        $data['sign'] = $sign;
        $data['sign_type'] = 'RSA';
        $data['bank'] = $bank;
        self::$param = $data;
        unset($data['bank']);
        unset($reqData);
        return $data;
    }

    /**
     * 再次请求处理
     * @param $data
     * @return array|bool|object
     */
    public static function getQrCode($data)
    {
        //转换第一次请求参数
        $parma = self::$param;
        $prepayId = json_decode($data, true);
        if($prepayId['status'] == "1"){
            $datas['appsId'] = $parma['apps_id'];
            $datas['prepayId'] = $prepayId['info']['prepay_id'];
            $datas['payType'] = $parma['bank'];
            $datas['date'] = date("YmdHis");
            Curl::$request = $datas;
        } else {
            echo $data;
        }
        //第二次请求网关
        Curl::$url = 'http://service.kenuolife.com/service/pcpay/getPayInfo';
        //第二次请求
        $res = Curl::Request();
        //赋值给语言包
        $results = json_decode($res, true);
        if($results['status'] == "1"){
            $right['result'] = $results;
        } else {
            $right['status'] = $results['status'];
            $right['msg'] = $results['msg'];
        }
        return $right;
    }
}