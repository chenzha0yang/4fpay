<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Koudaizpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';


        $data['merchNo']     = $payConf['business_num'];
        $data['orderNo']     = $order;
        $data['amount']      = $amount;
        $data['outChannel']  = $bank;
        $data['product']     = 'kd';
        $data['returnUrl']   = $returnUrl;
        $data['notifyUrl']   = $ServerUrl;
        $data['userId']      = time();
        $signStr             = json_encode($data, 320);

        $post                = [];
        $post['sign']        = strtolower(self::getMd5Sign(base64_encode($signStr), $payConf['md5_private_key']));

        $post['context']     = $signStr;
        $post['encryptType'] = 'MD5';

        $posts['orderNo']       = $order;
        $posts['amount']      = $amount;
        $posts['data']        = json_encode($post);
        $posts['httpHeaders'] = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($post)),
        ];

        self::$resData = $payConf;

        unset($reqData);
        return $posts;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == '0') {
            $flag = (bool) md5(base64_decode($result['context']) . self::$resData['md5_private_key']) == $result['sign'];
            if ($flag) {
                $context_arr              = json_decode($result['context'], true); //转为数组格式
                $result['payUrl'] = $context_arr['code_url'];
            } else {
                $result['msg'] = '验签失败';
            }
        }
        return $result;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $res, $payConf)
    {
        $result     = json_decode($res['context'], true);
        $flag       = (bool) md5(base64_decode($res['context']) . $payConf['md5_private_key']) == $res['sign'];

        if ($flag && $result['orderState'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}