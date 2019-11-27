<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qifupay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //TODO: do something

        self::$reqType = 'curl';
        self::$method  = 'header';
        self::$resType = 'other';
        self::$unit = '2';
        self::$payWay  = $payConf['pay_way'];

        $data = [];
        $data['mch_id'] = $payConf['business_num'];           //商户号
        $data['out_trade_no'] = $order;                       //订单号
        $data['body'] = 'guaiguaigeiqian';                    //商品描述
        $data['total_fee'] = (Int)$amount * 100;              //金额
        $data['mch_create_ip'] = '127.0.0.1';
        $data['notify_url'] = $ServerUrl;                     //通知地址
        $data['nonce_str'] = (string) rand(1000,9999);        //随机数
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        
        // 获取登录token
        $tokurl = 'http://api.qftx1.com/api/auth/access-token';   
        $appid = $data['mch_id'];
        $secret = $payConf['private_key'];
        $url = $tokurl."?appid=".$appid."&secretid=".$secret;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tokenXml = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($tokenXml);
        $token = (string)$xml->{"token"};
        $post = [];
        $post['out_trade_no'] = $data['out_trade_no'];
        $post['total_fee'] = $data['total_fee'];
        $post['data'] = self::arrayToXml($data);              //..提交的参数
        $post['httpHeaders'] = [
            "Content-type: text/xml",                         //..设置curl头部提交信息
            "Authorization: Bearer ".$token
        ];
        unset($reqData);
        return $post;
    }

   //数组转XML
    public static function arrayToXml($arr)
    {  
       $xml = "<xml>";
       foreach ($arr as $key=>$val)
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

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if( $result['result_code'] == '0' ) {
            $result['code_url'] = $result["code_url"];
        }
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        ksort($data);
        $str = "";
        foreach ($data as $key => $val) {
            if($key == 'sign') {
                continue;
            }
            $str .= $key."=".$val."&";
        }
        $MySign = strtoupper(md5($str ."key=" . $payConf['private_key']));
        if ($data["status"] == '0' && $data["sign"] == $MySign) {
            return true;
        } else {
            return false;
        }
    }
}