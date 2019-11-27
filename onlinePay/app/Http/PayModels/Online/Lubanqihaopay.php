<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Lubanqihaopay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$isAPP = true;


        $data['amount'] = sprintf('%.2f', $amount);
        $data['out_trade_no'] = $order;

        $data['sign'] = self::getSign($payConf['md5_private_key'],$data);
        $data['account_id'] = $payConf['business_num'];//可以为空
        $data['content_type'] = 'json';
        $data['thoroughfare'] = $bank;
        $data['type'] = '3';
        $data['robin'] = '2';
        $data['callback_url'] = $ServerUrl;
        $data['success_url'] = $returnUrl;
        $data['error_url'] = $returnUrl;

      //  $data['sign']= strtoupper(self::getMd5Sign($signStr, $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }


    static function getSign ($key_id, $array)
    {
        $cipher ='';
        $data = md5(sprintf("%.2f", $array['amount']) . $array['out_trade_no']);
        $key[] ="";
        $box[] ="";
        $pwd_length = strlen($key_id);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++)
        {
            $key[$i] = ord($key_id[$i % $pwd_length]);

            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        return md5($cipher);
    }



    /**
     * 二维码链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response ){

        $responseData = json_decode($response,true);
        $data = [];
        if($responseData['code'] == '200' && self::$payWay == '3'){
            $data['url'] = $responseData['data']['alipay'];
        }else{
            $data['url'] = $responseData['data']['wechat'];
        }
        return $data;
    }


    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $arr['amount'] = sprintf('%.2f', $data['amount']);
        $arr['out_trade_no'] = $data['out_trade_no'];

        $mySign = self::getSign($payConf['md5_private_key'],$arr);

        if ($mySign == $sign && $data['status'] == "success") {
            return true;
        }
        return false;
    }
}