<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Xiaotianepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];
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

        self::$isAPP = true;
        self::$reqType ='curl';
        self::$payWay = $payConf['pay_way'];
        self::$method = 'get';

        $data = array(
            'nonce_str'  => md5(time().rand(100000,999999)),//生成小于32位随机字符串
            'body'       => 'goodsname',
            'trade_type'     => $bank,
            'out_trade_no'    => $order,
            'total_fee'      => $amount,
            'spbill_create_ip' => self::getIp(),//用户的IP,
            'platform_code'    => $payConf['business_num']
        );
        $signStr =  self::getSignStr($data, true, true);
        $data['sign']=strtoupper(md5($signStr."&key=".$payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        libxml_disable_entity_loader(true);
        //先把xml转换为simplexml对象，再把simplexml对象转换成 json，再将 json 转换成数组。
        $data = json_decode(json_encode(simplexml_load_string($arr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        //$xmlData = $_REQUEST;
        self::$resData = $data;
        return $data;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $datas, $payConf)
    {
        $data = self::$resData;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  self::getSignStr($data, true,true);
        $signTrue = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名
        if (strtoupper($sign) == $signTrue && $data['result_code'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}