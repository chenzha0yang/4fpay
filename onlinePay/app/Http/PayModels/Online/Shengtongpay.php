<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shengtongpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        //self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $payInfo            = explode('@', $payConf['business_num']);

        if(!isset($payInfo[1])){
            echo '绑定格式错误！请参考：商户号@机构号';exit();
        }

        $data['txcode']  = 'F60002';
        $data['txdate']  = date('Ymd', time());
        $data['txtime']  = date('His', time());
        $data['version'] = '2.0.0';
        switch ($payConf['pay_way']) {
            case 2:
                if (self::$isAPP) {
                    $data['field003'] = '900030';
                } else {
                    $data['field003'] = '900021';
                }
                break;
            case 3:
                if (self::$isAPP) {
                    $data['field003'] = '900022';
                } else {
                    $data['field003'] = '900022';
                }
                break;
            case 4:
                $data['field003'] = '900028';
                break;
            case 6:
                $data['field003'] = '900029';
                break;
            case 7:
                if (self::$isAPP) {
                    $data['field003'] = '900035';
                } else {
                    $data['field003'] = '900031';
                }

                break;
            case 9:
                $data['field003'] = '900023';
                break;
            case 1:
                $data['field003'] = '900033';
                break;
            default:
                $data['field003'] = '900025';
                break;
        }
        $data['field004'] = (string) ($amount * 100);
        if (self::$isAPP && self::$payWay == 2) {
            //安卓:000001，苹果:000002
            if (self::get_device_type() == 'ios') {
                $data['field011'] = '000002';
            } else {
                $data['field011'] = '000001';
            }
        } else {
            $data['field011'] = '000000';
        }
        
        $data['field031'] = $bank;
        $data['field035'] = self::getIp();
        $data['field041'] = $payInfo[1];
        $data['field042'] = $payInfo[0];
        $data['field048'] = $order;
        $data['field060'] = $ServerUrl;
        // $data['field060'] = $returnUrl;
        $data['field125'] = $payInfo[0] . $order;
        $signStr          = '';
        foreach ($data as $value) {
            $signStr .= $value;
        }
        $signStr          = strtoupper(md5($signStr . $payConf['md5_private_key']));
        $data['field128'] = substr($signStr, 0, 16);

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['field048'] = $data['field048'];
        $postData['field004']  = $data['field004'];

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['field039'] == '00') {
            $data['qrCode'] = $data['field055'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $field055 = explode('|', $res['field055']);
        $res['orderid']  = $field055[0];
        $res['amount']   = $field055[3] / 100;
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $field055 = explode('|', $data['field055']);
        $rescode  = $field055[1];
        $orderId  = $field055[0];
        $amount   = $field055[3] / 100;
        $sign     = $data['field128'];
        $signStr  = $data['txcode'] . $data['txdate'] . $data['txtime'] . $data['version'];
        foreach ($data as $key => $value) {
            if (!in_array($key, ['txcode', 'txdate', 'txtime', 'version', 'field128'], true)) {
                $signStr .= $value;
            }
        }
        $signStr  = strtoupper(md5($signStr . $payConf['md5_private_key']));
        $signTrue = substr($signStr, 0, 16);
        if (strtoupper($sign) == strtoupper($signTrue)  && $rescode == '00') {
            return true;
        }
        return false;
    }

    public static  function get_device_type()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {

            return 'ios';

        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            return 'Android';
        } else {
            return 'other';
        }
    }

}