<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Yaoqianshupay extends ApiModel
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
        //$bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        $data = [
            'mch_id'     => $payConf['business_num'],  //商户号
            'goods'      => 'VIP',
            'order_no'   => $order,                    //订单号
            'amount'     => sprintf('%.2f',$amount),               //金额
            'notify_url' => $ServerUrl,                //异步通知地址
        ];
        if($payConf['pay_way'] == 2){ //微信接口类型
            $data['service'] = 'wx.activescan.pay';
        }
        if($payConf['pay_way'] == 3){   //支付宝接口类型
            $data['service'] = 'ali.activescan.pay';
        }
        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        $xml = self::arrayToXml($data);
        //将xml转换成数组 - 填写请求语言包
        $post = [];
        $post['xml']      = $xml;
        $post['order_no'] = $data['order_no'];
        $post['amount']   = $data['amount'];
        $post['url']      = $reqData['formUrl'];

        unset($reqData);
        return $post;
    }

    /**
     * 请求、二维码、链接处理
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
        //重新组合数组
        if($result){
            $data['mch_id']     = $result['mch_id'];
            $data['goods']      = $result['goods'];
            $data['order_no']   = $result['order_no'];
            $data['amount']     = $result['amount'];
            $data['notify_url'] = $result['notify_url'];
            $data['service']    = $result['service'];
            $data['sign']       = $result['sign'];
        }
        //将数组转换成xml
        $xmls = self::arrayToXml($data);
        Curl::$header = [
            'Content-Type: application/xml',
        ];
        Curl::$request = $xmls;
        //请求网关
        Curl::$url = $response['data']['url'];
        //请求
        $res = Curl::Request();
        libxml_disable_entity_loader(true);
        $responseData = json_decode(json_encode(simplexml_load_string($res['body'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($responseData["res_code"] == "100"){
            $data['code_url'] = $responseData['code_url'];
        }else{
            $data['res_code'] = $responseData['res_code'];
            $data['res_msg']  = $responseData['res_msg'];
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