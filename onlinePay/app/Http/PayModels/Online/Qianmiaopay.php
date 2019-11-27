<?php
/**
 * Created by PhpStorm.
 * User: JS-00036
 * Date: 2018/9/14
 * Time: 14:56
 */

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Qianmiaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order = $reqData['order'];  //订单号
        $amount = $reqData['amount'];  //金额
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        self::$unit = 2;
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        $data = [
            'mch_id'        => $payConf['business_num'],    //商户号
            'out_trade_no'  => $order,                      //订单号
            'body'          => 'VIP',                       //商品描述
            'total_fee'     => $amount * 100,               //金额
            'mch_create_ip' => '127.0.0.1',                 //IP
            'notify_url'    => $ServerUrl,
            'nonce_str'     => self::randStr(32),
        ];

        $signStr = self::getSignStr($data, true, true); //排序拼接字符串
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //加密

        //获取token
        $token_url = 'http://52.194.205.7:8080/api/auth/access-token';
        //第一次请求路径
        $url = $token_url."?appid=".$data['mch_id']."&secretid=".$payConf['md5_private_key'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $datat = curl_exec($curl);
        curl_close($curl);
        libxml_disable_entity_loader(true);
        $res = json_decode(json_encode(simplexml_load_string($datat, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $data['token'] = $res['token'];
        //将token值放进数组转换成xml
        $xml = self::arrayToXml($data);

        //将xml转换成数组 - 填写请求语言包
        $post = [];
        $post['xml'] = $xml;
        $post['out_trade_no'] = $data['out_trade_no'];
        $post['total_fee'] = $data['total_fee'];
        //第二次请求 - 网关
        $post['url'] = $reqData['formUrl'];
        return $post;
    }


    /**
     * 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        Curl::$method = 'header';
        Curl::$headerToArray = true;
        //将xml转换成数组
        $xml = $response['data']['xml'];
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        //重新组合数组 - 因为上一步结果有token值，第二次请求不需要带token
        if($result){
            $data['mch_id']        = $result['mch_id'];
            $data['out_trade_no']  = $result['out_trade_no'];
            $data['body']          = $result['body'];
            $data['total_fee']     = $result['total_fee'];
            $data['mch_create_ip'] = $result['mch_create_ip'];
            $data['notify_url']    = $result['notify_url'];
            $data['nonce_str']     = $result['nonce_str'];
            $data['sign']          = $result['sign'];
        }
        //将数组转换成xml
        $xmls = self::arrayToXml($data);
        Curl::$header = [
            'Content-Type: application/xml',
            'Authorization: Bearer '.$result['token'],
        ];
        Curl::$request = $xmls;
        //第二次请求网关
        Curl::$url = $response['data']['url'].'?token='.$result['token'];
        //第二次请求
        $res = Curl::Request();
        libxml_disable_entity_loader(true);
        $responseData = json_decode(json_encode(simplexml_load_string($res['body'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($responseData["status"] == "0" && $responseData["result_code"] == "0"){
            $data['code_url'] = $responseData['code_url'];
        }else{
            $data['status'] = $responseData['status'];
            $data['message']  = $responseData['message'];
        }
        return $data;
    }

    /**
     * 数组 转 xml
     * @param $data
     * @return string
     */
    public static function arrayToXml($data){
        $xml = "<xml>";
        foreach ($data as $key => $val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
}