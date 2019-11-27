<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hipay extends ApiModel
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
        self::$method = 'get';
        //TODO: do something

        $data['account'] = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        $data['type'] = $bank;
        $data['amount'] = $amount;
        $data['merchantNo'] = $payConf['business_num'];
        $data['key'] = $payConf['md5_private_key'];
        $data['notifyUrl'] = $ServerUrl;
        $data['merchantOrderNo'] = $order;
        // 组装pr
        $origin_data = self::getParameterStr($data);
        $pub_key     = openssl_pkey_get_public($payConf['public_key']);
        $dataStr = openssl_public_encrypt($origin_data, $encrypted,$pub_key) ? base64_encode($encrypted) : null;
        $str['pr']=$dataStr;
        //sign签名
        $str['sign'] = self::getParameterMd5($data);
        unset($reqData);
        return $str;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signTrue = md5("amount=".$data['amount']."&merchantOrderNo=".$data['merchantOrderNo']."&platOrderNo=".$data['platOrderNo']."&status=".$data['status']."&key=".$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     *获取md5签名参数
     */
    public static function getParameterMd5($data){
        //末尾的字符串是三方写死的干扰字符串不能更换
        $parameter = 'account='.$data['account'].'&amount='.$data['amount'].'&key='.$data['key'].'&merchantOrderNo='.$data['merchantOrderNo'].'&merchantNo='.$data['merchantNo'].'&notifyUrl='.$data['notifyUrl'].'&type='.$data['type'].'8ab211a6536711e893d10242ac11as02';
        return md5($parameter);
    }

    /**
     *获取待RSA加密的参数字符串
     */
    public static function getParameterStr($data){
        $parameter = 'account='.$data['account'].'&merchantNo='.$data['merchantNo'].'&merchantOrderNo='.$data['merchantOrderNo'].'&notifyUrl='.$data['notifyUrl'].'&type='.$data['type'].'&amount='.$data['amount'].'&key='.$data['key'];
        return $parameter;
    }

}