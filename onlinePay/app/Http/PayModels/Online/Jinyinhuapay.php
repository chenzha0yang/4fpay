<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinyinhuapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $imgSrc = true;

    public static $changeUrl = true;

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

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $data['merchantCode'] = $payConf['business_num'];
        $data['merchantOrderNo'] = $order;
        $data['payType'] = $bank;
        $data['payPrice'] = $amount*100;
        $data['merchantName'] = $payConf['business_num'];
        $data['callbackUrl']    = $ServerUrl;//异步通知 URL

        $data['sign'] = md5('merchantName='.$data['merchantName'].'&merchantCode='.$data['merchantCode'].'&merchantOrderNo='.$data['merchantOrderNo'].'&payPrice='.$data['payPrice'].'&payType='.$data['payType'].'&'.$payConf['md5_private_key']);
        $post['queryUrl'] = $reqData['formUrl'].'/jyh-restful/server/create/pay/order';
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $post['merchantOrderNo'] = $data['merchantOrderNo'];
        $post['payPrice'] = $data['payPrice'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['result'] == '0') {
            $result['payPic'] =  $result['datas']['payPic'];
        }
        return $result;

    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $data = $request->all();
        $data['payPrice'] = $data['payPrice'] / 100;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signTrue = md5('merchantName='.$data['merchantName'].'&merchantOrderNo='.$data['merchantOrderNo'].'&noticeType='.$data['noticeType'].'&payPrice='.$data['payPrice'].'&payType='.$data['payType'].'&'.$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['noticeType'] =='1') {
            return true;
        } else {
            return false;
        }
    }

}