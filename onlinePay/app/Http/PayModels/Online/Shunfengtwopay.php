<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shunfengtwopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = []; // 判断是否跳转APP 默认false

    public static $is_app = false; // 判断是否跳转APP 默认false



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

        if ($payConf['is_app'] == 1 && $bank =='26' ) {
            self::$is_app = true;
        }
        self::$isAPP = true;
        self::$reqType =  'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';


        $data['serverNum'] = $payConf['business_num'];
        $data['type'] = $bank;
        $data['amount'] = $amount;
        $data['transNumber'] = $order;
        $data['notify'] = $ServerUrl;
        $data['sign'] = md5( md5($data['serverNum']. $data['amount'].$data['transNumber'].$data['type'].$data['notify']).$payConf['md5_private_key']);

        $post['data'] = json_encode($data);
        $post['httpHeaders'] = [
            'Content-Type: application/json; charset=utf-8',
        ];
        $post['amount'] = $data['amount'];
        $post['transNumber'] = $data['transNumber'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($req)
    {
        $data = json_decode($req, true);
        if($data['code'] == '200'){
            $data['payUrl'] = urldecode($data['data']['payUrl']);
        }
        if(self::$is_app){
            echo "<script type='text/javascript'>window.location.href('".$data['payUrl']."')</script>";
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $json = $request->getContent();
        $data = json_decode($json,true);
        self::$array = $data;
        return $data;
    }


    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        $isSign = md5(md5($data['amount'].$data['payType'].$data['serverNum'].$data['transNumber'].$data['payStatus']).$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($isSign) && $data['payStatus'] == '1') {
            return true;
        }
        return false;
    }


}
