<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Jiubapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false;

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

        //TODO: do something

        $data['partner_no']   = $payConf['business_num']; //商户号
        $data['mch_order_no'] = $order; //订单号
        $data['body']         = 'chongzhi';
        $data['detail']       = '';
        $data['money']        = (Int)$amount * 100; //金额
        $data['attach']       = '';
        $data['callback_url'] = $ServerUrl; //异步通知地址
        $data['time_stamp']   = self::getMillisecond(); //13位时间戳
        $data['code_type']    = (Int)$bank; //支付类型
        $tokenFields          = array(
            "partner_no"   => $data['partner_no'],
            "mch_order_no" => $data['mch_order_no'],
            "time_stamp"   => $data['time_stamp'],
            "money"        => $data['money'],
        );
        $signStr              = self::getSignStr($tokenFields, false, true);
        $data['token']        = md5($signStr . "&key=" . $payConf['md5_private_key']);

        self::$isAPP = true;
        self::$resType   = 'other';
        self::$unit = 2;
        self::$payWay  = $payConf['pay_way'];
        $url = $reqData['formUrl'];
        $jsonRes = self::fileGetReq($url,$data);
        $post['json']     = $jsonRes;
        $post['mch_order_no']  = $data['mch_order_no'];
        $post['money'] = $data['money'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($res)
    {
        $post = $res['data']['json'];
        if ($post) {
            $data = json_decode($post,true);
            if(isset($data['code']) && (int)$data['code'] == 0){
                $codeLink = $data['code_link'];
                $http                     = str_replace("HTTPS", "HTTP", $codeLink);
                $data['code_link'] = $http;
            }else{
                $data['code'] = $data['code'];
                $data['msg']  = $data['msg'];
            }
        } else {
            $data['code'] = '500';
            $data['msg']  = '请求失败,请联系管理员';
        }
        return $data;
    }

    private static function fileGetReq($url,$postData)
    {
        $queryData = http_build_query($postData);
        $options   = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type:application/x-www-form-urlencoded',
                'content' => $queryData,
                'timeout' => 60,// 超时时间（单位:s）
            ),
            "ssl"  => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        $context   = stream_context_create($options);
        ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.2');
        return file_get_contents($url, false, $context);
    }


    public static function getVerifyResult(Request $request, $mod)
    {
        $data = $request->all();
        if(empty($data['money'])){
            return false;
        }
        $data['money'] = $data['money'] / 100;
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $tokenFields = array(
            "money" => $data["money"],
            "mch_order_no" => $data["mch_order_no"],
            "order_no" => $data["order_no"],
            "time_stamp" => $data["time_stamp"]
        );
        $signStr       = self::getSignStr($tokenFields, false, true);
        $myToken = md5($signStr . "&key=" . $payConf['public_key']);
        if (strtoupper($myToken) == strtoupper($data['token']) ){
            return true;
        } else {
            return false;
        }
    }



    /**
     * @return float
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}