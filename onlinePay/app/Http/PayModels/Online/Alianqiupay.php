<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Alianqiupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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

        $pikey = openssl_get_privatekey($payConf['rsa_private_key']);
        if (!$pikey) {
            echo '私钥绑定格式错误!请仔细检查';exit();
        }
        $pukey = openssl_get_publickey($payConf['public_key']);
        if(!$pukey){
            echo '公钥绑定格式错误!请仔细检查';exit();
        }

        $data['alq_merc_id'] = $payConf['business_num'];//商户号
        $data['alq_order_id'] = $order;
        $data['alq_order_amount'] = $amount;
        $data['alq_notify_url'] = $ServerUrl;

        $signature = '';
        ksort($data);
        $text = '';
        $symbol = '';
        foreach ($data as $key => $item) {
            $text .= $symbol . $key . '=' . $item;
            $symbol = '&';
        }
        openssl_sign($text, $signature, $pikey, OPENSSL_ALGO_SHA1);
        openssl_free_key($pikey);

        $data['alq_sign'] = base64_encode($signature);
        $data['alq_back_url'] = $returnUrl;
        $data['alq_pay_type'] = $bank;
        $data['alq_desc'] = '';
        $data['alq_extra'] = '';

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }
    
    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign = $data['alq_sign'];
        unset($data['alq_sign']);
        unset($data['alq_desc']);
        unset($data['alq_attch']);
        unset($data['alq_datetime']);
        unset($data['alq_timezone']);
        unset($data['alq_extra']);
        unset($data['alq_timestamp']);

        $pukey = openssl_get_publickey($payConf['public_key']);
        ksort($data);
        $text = '';
        $symbol = '';
        foreach ($data as $key => $item) {
            $text .= $symbol . $key . '=' . $item;
            $symbol = '&';
        }
        $result = openssl_verify($text, base64_decode($sign), $pukey);
        openssl_free_key($pukey);

        if ($result && $data['alq_status_code'] == '200') {
            return true;
        }
        return false;
    }


}