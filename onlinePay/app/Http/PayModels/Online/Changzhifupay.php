<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Changzhifupay extends ApiModel
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
        self::$resType = 'other';
        self::$postType = true;


        $data['sign_type']   = 'md5';
        $data['mch_id']      = $payConf['business_num'];
        $data['mch_order']   = $order;
        $data['amt']         = $amount * 1000;
        $data['remark']      = $order;
        $data['created_at']  = time();
        $data['client_ip']   = self::getIp();
        $data['payType']     = $bank;
        $data['notify_url']  = $ServerUrl;
        $data['mch_key'] = $payConf['md5_private_key'];
        $signStr             = self::getSignStr($data,true,true);
        $data['sign']        = strtolower(self::getMd5Sign($signStr, ''));
        unset($data['mch_key']);
        $data['relAmount'] = $amount;

        unset($reqData);
        return $data;
    }

    public static function getRequestByType ($data) {
        unset($data['relAmount']);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == 1) {
            if (self::$isAPP) {
                if(isset($result['data']['code_url'])){
                    $res['payUrl'] = $result['data']['code_url'];
                }elseif(isset($result['data']['pay_info'])){
                    $res['payUrl'] = $result['data']['pay_info'];
                }elseif(isset($result['data']['redirect_pay_url'])){
                    $res['payUrl'] = $result['data']['redirect_pay_url'];
                }else{
                    $res['payUrl'] = $result['data']['code_img_url'];
                }
            } else {
                if(isset($result['data']['code_url'])){
                    $res['codeUrl'] = $result['data']['code_url'];
                }elseif(isset($result['data']['pay_info'])){
                    $res['codeUrl'] = $result['data']['pay_info'];
                }elseif(isset($result['data']['redirect_pay_url'])){
                    $res['payUrl'] = $result['data']['redirect_pay_url'];
                }else{
                    $res['codeUrl'] = $result['data']['code_img_url'];
                }
            }
        } else {
            $res['message'] = $result['msg'];
            $res['code'] = $result['code'];
        }
        return $res;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->all();
        $arr['mch_amt'] = $arr['mch_amt'] / 1000;
        return $arr;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $data['mch_key'] = $payConf['md5_private_key'];
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtolower(self::getMd5Sign($signStr, ''));

        if (strtolower($sign) == $signTrue && $data['status'] == 2) {
            return true;
        } else {
            return false;
        }
    }
}