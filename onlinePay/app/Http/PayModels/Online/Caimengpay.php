<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\File;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use Illuminate\Http\Request;

class Caimengpay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['merOrderNo']   = $order;
        $data['amount']       = sprintf('%.2f',$amount);
        $data['payType']     = $bank;
        $data['businessType'] = 1;
        $data['notifyUrl'] = $ServerUrl;
        $data['expireTime'] = 10;
        $data['bankCode'] = 'SPABANK';
        $data['orderIp'] = self::getIp();
        $data['submitTime'] = self::getMillisecond();

        ksort($data);
        $http_build = str_replace('%3A%2F%2F','://',urldecode(http_build_query($data)));
        $data['sign'] = strtoupper(md5($http_build . '&key=' . $payConf['md5_private_key']));

        $jsonData = json_encode($data,JSON_UNESCAPED_UNICODE);
        $crypto='';
        //分段加密
        foreach (str_split($jsonData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encrypted, $payConf['public_key']);
            $crypto .= $encrypted;
        }
        $post['data'] =  $crypto?base64_encode($crypto):null;
        $post['merId']      = $payConf['business_num'];
        $post['version'] = '1.1';

        $postJson = json_encode($post);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postJson),
        ];
        $postData['data']         = $postJson;
        $postData['httpHeaders']  = $header;
        $postData['merOrderNo'] = $data['merOrderNo'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '200') {
            $data['payUrl'] = $data['data']['payUrl'];
        }
        return $data;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        $res=json_decode($arr,true);
        if(isset($res['merOrderNo'])){
            $bankOrder = PayOrder::getOrderData($res['merOrderNo']);//根据订单号 获取入款注单数据
            if (empty($bankOrder)) {
                //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
                File::logResult($res);
                echo 'success';die;
            }
            $payConf = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
            $crypto = '';
            foreach (str_split(base64_decode($res['data']), 128) as $chunk) {
                openssl_private_decrypt($chunk, $encrypted, $payConf['rsa_private_key']);
                $crypto.= $encrypted;
            }
            $data=json_decode($crypto,true);
            $data['merOrderNo'] = $res['merOrderNo'];
        }else{
            $data['merOrderNo'] = '';
            $data['amount'] = '';
            return $data;
        }
            return $data;
    }
    
    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $request=json_decode($json,true);
        $crypto = '';
        foreach (str_split(base64_decode($request['data']), 128) as $chunk) {
            openssl_private_decrypt($chunk, $encrypted, $payConf['rsa_private_key']);
            $crypto.= $encrypted;
        }
        $data=json_decode($crypto,true);

        $sign     = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $http_build = str_replace('%3A%2F%2F','://',urldecode(http_build_query($data)));
        $signTrue= strtoupper(md5($http_build . '&key=' . $payConf['md5_private_key']));

        if ($signTrue == strtoupper($sign) && $data['orderState'] == 1) {
            return true;
        }
        return false;
    }

    public static function getMillisecond() {
       list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格
       return (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);
    }
}