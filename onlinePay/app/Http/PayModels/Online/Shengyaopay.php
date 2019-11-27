<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shengyaopay extends ApiModel
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
        self::$unit = 2 ;       //单位：分
        self::$postType = true ;       //JSON格式提交
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        $data = array(
            'Merchants'         =>      $payConf['business_num'],       //商户号
            'Description'       =>      'dulex',                        //商品名称
            'BusinessOrders'    =>      $order,                         //订单号
            'Amount'            =>      $amount * 100,                  //金额
            'SubmitIP'          =>      '127.0.0.1',        //客户端 IP
            'ReturnUrl'         =>      $returnUrl,                     //同步通知地址
            'NotifyUrl'         =>      $ServerUrl,                     //异步通知地址
            'TypeService'       =>      $bank,                          //支付类型
            'PostService'       =>      'Scan',                         //支付端口:扫码
            'OrderTime'         =>      time(),  //支付时间
        );
        $data['Sign'] = self::SignCrypt($data,$payConf['rsa_private_key']);
        unset($reqData);
        return $data;
    }
    //加密数据
    public static function SignCrypt($value, $mKey) {
        ksort($value);
        $Data = '';
        foreach($value as $x=>$x_value) {
            $Data .= "$x=$x_value&";
        }
        if(substr($Data, -1) == '&') {
            $Data = substr($Data, 0, -1);
        }
        $Private_Key = openssl_get_privatekey($mKey);
        openssl_sign($Data, $Sign, $Private_Key, OPENSSL_ALGO_MD5);
        $Sign = base64_encode($Sign);
        return $Sign;
    }
    public static function getRequestByType($data)
    {
        return json_encode($data);
    }
}