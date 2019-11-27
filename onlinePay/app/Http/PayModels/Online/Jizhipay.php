<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Jizhipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $piKey = '';

    private static $UserName = '';
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        self::$piKey = $payConf['rsa_private_key'];
        self::$isAPP = true;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$method  = 'header';
        //TODO: do something
        $payInfo = explode('@', $payConf['business_num']);
        if(!isset($payInfo[1])){
            echo '商户号请填入格式为：appId@apptoken 的商户信息';exit;
        }
        $data['merchant_id'] = $payInfo[0];//商户号
        $data['order_no'] = $order;//订单号
        $data['amount'] = $amount;//订单金额
        $data['user_id'] = self::$UserName;
        $data['user_name'] = self::$UserName;
        $data['goods_name'] = 'shuangyin';

        $signStr =  self::getSignStr($data,true,false);
        $signStr .= '&';
        $signature = '';
        $privatekey = openssl_pkey_get_private($payConf['rsa_private_key']);
        $res=openssl_get_privatekey($privatekey);
        openssl_sign($signStr, $signature, $res,'SHA256');
        openssl_free_key($res);
        $sign  = base64_encode($signature);

        $data['sign'] = $sign;
        $data['pay_type'] = $bank;//银行编码
        if($payConf['pay_way'] == 0){
            $data['pay_type'] = 'OnlineBankPay';
            $data['bank_code'] = $bank;
        }
        $data['user_ip'] = self::getIp();
        $data['notify_url'] = $ServerUrl;

        $jsonData = json_encode($data);

        $header = array(
                'app-token:'.$payInfo[1],
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            );
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['order_no'] = $data['order_no'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $backData = json_decode($response, true);
        if ($backData['success']) {
            $backData['payUrl'] = $backData['data']['pay_url'];
        }

        return $backData;
    }

     public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,true);
        return $data;
    }

    /**
     * 回掉特殊处理
     * @param $model
     * @param $data - 返回的数据 - array
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);
        $sign = $data['sign'];
        $signStr =  'merchant_id='.$data['merchant_id'].'&serial_no='.$data['serial_no'].'&order_no='.$data['order_no'].'&amount='.$data['amount'].'&status='.$data['status'].'&create_time='.$data['create_time'].'&pay_completed_time='.$data['pay_completed_time'].'&actual_amount='.$data['actual_amount'].'&';
        $publickey = openssl_pkey_get_public($payConf['public_key']);
        $res=openssl_get_publickey($publickey);
        $re =  openssl_verify($signStr,base64_decode($sign),$res,'SHA256');

        if($re && $data['status'] == '3'){
            return true;
        }else{
            return false;
        }
    }
}