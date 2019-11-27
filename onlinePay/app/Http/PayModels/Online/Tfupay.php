<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址



        $data['mchtId'] = $payConf['business_num'];
        $data['version'] = '20';
        $data['biz'] = $bank;
        $data['orderId'] = $order;
        $data['orderTime'] = date('YmdHis');
        $data['amount'] = $amount * 100;
        $data['currencyType'] = 'CNY';
        $data['goods'] = 'goods';
        $data['notifyUrl'] = $ServerUrl;
        $data['ip'] = self::getIp();
        $data['callBackUrl'] = $returnUrl;

        $strSign = 'amount=' . $data['amount'] . '&callBackUrl=' . $data['callBackUrl'] . '&currencyType=' . $data['currencyType'] . '&goods=' . $data['goods']
            . '&ip=' . $data['ip'] . '&notifyUrl=' . $data['notifyUrl'] . '&orderId=' . $data['orderId'] . '&orderTime=' . $data['orderTime'] . '&key=' .$payConf['md5_private_key'];

        $data['sign']           = strtoupper(md5($strSign ));
        unset($reqData);
        return  $data;

    }


    public static function getVerifyResult($request, $mod)
    {
        $a=$request->getContent();
        $data = json_decode($a,true);
        $arr['amount']  = $data['body']['amount'] /100;
        $arr['order']  = $data['body']['orderId'];
        return $arr;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $post     = file_get_contents("php://input");
        $data     = json_decode($post, true);
        $sign = $data['sign'];
        unset($data['sign']);
        $isSign = strtoupper(md5('amount='.$data['body']['amount'].
            '&biz='.$data['body']['biz'].
            '&chargeTime='.$data['body']['chargeTime'].
            '&mchtId='.$data['body']['mchtId'].
            '&orderId='.$data['body']['orderId'].
            '&payType='.$data['body']['payType'].
            '&seq='.$data['body']['seq'].
            '&status='.$data['body']['status'].
            '&tradeId='.$data['body']['tradeId'].
            '&key='. $payConf['md5_private_key']
        ));

        if (strtoupper($sign) == $isSign && $data['body']['status'] == 'SUCCESS') {
            return true;
        }
        return false;
    }

}