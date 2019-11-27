<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Goldbpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $context               = [];
        $context['product']    = 'goodsName';
        $context['amount']     = sprintf('%.2f', $amount);
        $context['orderNo']    = $order;
        $context['merchNo']    = $payConf['business_num'];
        $context['memo']       = 'pay:' . $context['amount'];
        $context['notifyUrl']  = $ServerUrl;
        $context['currency']   = 'CNY';
        $context['reqTime']    = date('YmdHis');
        $context['title']      = 'title';
        $context['userId']     = (string)time();
        $context['outChannel'] = $bank;//银行编码
        $data['context'] = json_encode($context,JSON_UNESCAPED_UNICODE);
        $data['sign'] = md5($data['context'] . $payConf['md5_private_key']);
        $data['encryptType'] = 'MD5';

        $post['data']        = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8'
        );
        $post['orderNo'] = $context['orderNo'];
        $post['amount']     = $context['amount'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if ($data['code'] == '0') {
            $context = $data['context'];
            $data['code_url'] = $context['code_url'];
        }else{
            $data['msg'] = '支付异常';
            $data['code'] = $data['code'];
        }
        return $data;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $post    = file_get_contents("php://input");
        $data = json_decode($post,true);
        $res = json_decode(base64_decode($data['context']), true);
        $sign = $data['sign'];

        $signStr = md5(base64_decode($data["context"]).$payConf['md5_private_key']);
        if ($signStr == $sign && $res['orderState'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}