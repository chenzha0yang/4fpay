<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shunfupay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        $data = [];
        $data['merNo'] = $payConf['business_num'];                   //商户号
        $data['payNetway'] = $bank;                      //WX 或者 ZFB
        $data['random'] = (string) rand(1000,9999);      //4位随机数    必须是文本型
        $data['orderNo'] = $order;                       //商户订单号
        $data['amount'] = (string) ($amount*100);            //默认分为单位
        $data['goodsInfo'] = 'chongzhi';                 //商品名称
        $data['callBackUrl'] = $ServerUrl;               //通知地址 可以写成固定
        $data['callBackViewUrl'] = $returnUrl;           //前台跳转 可以写成固定
        $data['clientIP'] = "127.0.0.1";                 //客户请求IP
        ksort($data);                                    //排列数组 将数组已a-z排序
        $sign = md5(self::json_encode($data) . $payConf['md5_private_key']); //生成签名
        $data['sign'] = strtoupper($sign);               //设置签名

        $map = self::json_encode($data); #将数组转换为JSON格式

        $data['data'] = $map;
        self::$payWay  = $payConf['pay_way'];
        self::$reqType = 'curl';
        self::$unit    = 2;
        unset($reqData);
        return $data;
    }

    /**
     * @param $input
     * @return string
     */
    public static function json_encode($input)
    {
        if(is_string($input)){
            $text = $input;
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            $text = str_replace("\\/", "/", $text);
            return '"' . $text . '"';
        }else if(is_array($input) || is_object($input)){
            $arr = array();
            $is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
            foreach($input as $k=>$v){
                if($is_obj){
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                }else{
                    $arr[] = self::json_encode($v);
                }
            }
            if($is_obj){
                $arr = str_replace("\\/", "/", $arr);
                return '{' . join(',', $arr) . '}';
            }else{
                $arr = str_replace("\\/", "/", $arr);
                return '[' . join(',', $arr) . ']';
            }
        }else{
            $input = str_replace("\\/", "/", $input);
            return $input . '';
        }
    }

    /**
     * @param $json
     * @return mixed
     */
    public static function json_decode($json)
    {
        $comment = false;
        $out = '$x=';
        for ($i=0; $i<strlen($json); $i++){
            if (!$comment){
                if (($json[$i] == '{') || ($json[$i] == '[')) $out .= ' array(';
                else if (($json[$i] == '}') || ($json[$i] == ']')) $out .= ')';
                else if ($json[$i] == ':') $out .= '=>';
                else $out .= $json[$i];
            }
            else $out .= $json[$i];
            if ($json[$i] == '"' && $json[($i-1)]!="\\") $comment = !$comment;
        }
        eval($out . ';');
        return $x;
    }

    /**
     * @param $request
     * @return array
     */
    public static function getVerifyResult($request)
    {
        $res = $request->all();
        $res['amount'] = (int) $res['amount']/100;
        return $res;
    }

    /**
     * @param $mod
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod,$sign,$payConf)
    {
        $reSign = $sign['sign']; #保留签名数据
        $arr = array();
        foreach ($sign as $key=>$v){
            if ($key !== 'sign'){ #删除签名
                $arr[$key] = $v;
            }
        }
        ksort($arr);
        $mdSign = strtoupper(md5(json_encode($arr) . $payConf['md5_private_key'])); #生成签名
        if ($reSign == $mdSign) {
            if($sign['resultCode'] == '00'){
                return true;
            }
        }else{
            return false;
        }
    }
}