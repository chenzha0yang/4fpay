<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Cntpay extends ApiModel
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

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';


        $useapp    = explode('@', $payConf['business_num']);
        $data['userId'] = $useapp['0'];
        $data['merchantUserID'] = time();
        $data['number'] = number_format(floatval($amount), 2, '.', '');
        $data['userOrder'] = $order;
        $data['payType'] = $bank;
        if($payConf['pay_way'] == '1'){
            $data['payType'] = '3';
        }
        $data['isPur'] = '1';
        $data['remark'] = 'cnt';
        $data['appID'] = $useapp['1'];
        $data['ckValue'] = md5($data['userId'] . "|" . $data['merchantUserID'] . "|" . $data['userOrder'] . "|" . $data['number'] . "|" . $data['payType'] . "|" . $data['isPur'] . "|" . $data['remark'] . "|" . $data['appID'] . "|" . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['resultCode'] == '00') {
            $result['payurl'] = $result['data']['payPage'];
        }
        return $result;
    }

    public static function SignOther($type, $data,$payConf){
        $sign    = md5($data['userId'].'|'.$data['orderId'].'|'.$data['userOrder'].'|'.$data['number'].'|'.$data['merPriv'].'|'.$data['remark'].'|'.$data['date'].'|'.$data['resultCode'].'|'.$data['resultMsg'].'|'.$data['appID'].'|'.$payConf['md5_private_key']);
        if ($sign == $data['chkValue'] && $data['resultCode'] == '0000') {
            return true;
        } else {
            return false;
        }
    }
}