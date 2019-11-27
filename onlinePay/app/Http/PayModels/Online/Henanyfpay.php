<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Henanyfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0;  //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*    */
    public static $reqData = [];

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

        //TODO: do something
        self::$resType  = 'other';
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$isAPP    = true;

        $data                    = [];
        $data['service']         = $bank;      //支付类型
        $data['paytype']         = 'Wap';      //支付方式
        $data['version']         = 'v2.0';     //版本号
        $data['signtype']        = 'MD5';      //签名类型
        $data['charset']         = 'UTF-8';    //字符集
        $data['merchantid']      = $payConf['business_num']; //商户号
        $data['userid']          = time();     //用户ID
        $data['username']        = isset($reqData['username']) ? $reqData['username'] : 'diaodeyibi'; //用户名
        $data['shoporderId']     = $order;  //订单号
        $data['totalamount']     = number_format(floatval($amount), 2, '.', '');   //金额
        $data['productname']     = 'hf';
        $data['notify_url']      = $ServerUrl;   //异步通知地址
        $data['callback_url']    = $returnUrl;    //同步返回
        $data['nonce_str']       = mt_rand(time(), time() + rand());
        $signStr                 = self::getSignStr($data, true, true);
        $data['sign']            = strtoupper(self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']));
        $postData['data']        = $data;
        $postData['shoporderId'] = $data['shoporderId'];
        $postData['totalamount'] = $data['totalamount'];
        unset($reqData);
        return $postData;
    }


    /**
     * 请求参数整理
     * @param $post
     * @return mixed
     */
    public static function getRequestByType($post)
    {
        return json_encode($post['data'], JSON_FORCE_OBJECT);;
    }


    /**
     * 请求参数返回处理
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp['data']['res'], true);
        if ($result['result'] == '0') {
            $result['payPic'] = $result['datas']['payPic'];
        }
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signStr  = strtoupper(self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']));
        if($signStr == strtoupper($sign) && $data['status'] == '0' && $data['result_code'] == '0'){
            return true;
        } else {
            return false;
        }
    }
}