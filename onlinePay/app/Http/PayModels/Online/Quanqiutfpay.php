<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\CallbackMsg;

class Quanqiutfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];

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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1 || $payConf['pay_way'] == 3) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $data = [];
        $data['merId'] = $payConf['business_num'];
        $data['businessOrderId'] = $order;
        $data['tradeMoney'] = $amount;
        $data['payType'] = $bank;
        $data['asynURL']= $ServerUrl . '/' . $payConf['agent_id'] . '_' . $payConf['agent_num'];

        $post['data']        = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $post['businessOrderId'] = $data['businessOrderId'];
        $post['tradeMoney']     = $data['tradeMoney'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if ($data['code'] == '1000') {
            $data['codeurl'] = $data['info']['codeurl'];
            $data['pcodeurl'] = $data['info']['pcodeurl'];
        }

        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $agent = $request->agent;
        if (!$agent) {
            CallbackMsg::addDebugLogs(array('未获取到代理线'),$request->url());
            echo trans("success.{$mod}");die;
        }
        $error = [];
        $decodes = str_split(base64_decode($data['encrypt']), 256);
        $pem_private_path = resource_path() . '/key/' . $agent . '/qqtf_private_key.pem';

        if (!file_exists($pem_private_path)) {
            // 文件不存在
            CallbackMsg::addDebugLogs(array('对应私钥文件不存在'),$pem_private_path);
            echo trans("success.{$mod}");die;
        }
        $priKey = file_get_contents($pem_private_path);


        $strNull = "";
        $dcyCont = "";
        foreach ($decodes as $decode) {
            if (!openssl_private_decrypt($decode, $dcyCont, $priKey)) {
                $error['msg'] = '回调解密失败：'  . openssl_error_string();
                CallbackMsg::addDebugLogs($error);
                echo trans("success.{$mod}");die;
            }
            $strNull .= $dcyCont;
        }
        self::$signData['sign'] = $data['sign'];
        self::$signData['content'] = $strNull;

        $req = json_decode($strNull,true);
        return $req;

    }

    public static function signOther($mod, $data, $payConf)
    {
        $pem_public_path = resource_path() . '/key/' . $payConf['agent_id'] . '_' . $payConf['agent_num'] . '/qqtf_public_key.pem';

        if (!file_exists($pem_public_path)) {
            // 文件不存在
            CallbackMsg::addDebugLogs(array('对应公钥文件不存在'),$pem_public_path);
            echo trans("success.{$mod}");die;
        }
        $pubKey = file_get_contents($pem_public_path);
        $sign = self::$signData['sign'];
        $check = (bool)openssl_verify(self::$signData['content'], base64_decode($sign), $pubKey, OPENSSL_ALGO_SHA1);
        return $check;
    }

}