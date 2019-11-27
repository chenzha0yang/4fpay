<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Chaopaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method = 'header';

        $data['product'] = 'skd';
        $data['amount'] = sprintf('%.2f',$amount);//订单金额
        $data['orderNo'] = $order;//订单号
        $data['merchNo'] = $payConf['business_num'];//商户号
        $data['memo'] = 'goods';
        $data['notifyUrl'] = $ServerUrl;
        $data['currency'] = 'CNY';
        $data['reqTime'] = date('YmdHis');
        $data['title'] = 'goods';
        $data['returnUrl'] = $returnUrl;
        $data['userId'] = self::$UserName;
        $data['outChannel'] = $bank;//银行编码
        if($payConf['pay_way'] == 1){
            $data['outChannel'] = 'wy';
            $data['bankCode'] = $bank;
        }
        $sign = md5(base64_encode(json_encode($data)).$payConf['md5_private_key']);
        //$data['bankCode'] = 'skd';
        $post_data = [
            'sign' => $sign,
            'context' => base64_encode(json_encode($data)),
            'encryptType' => 'MD5'
        ];

        $result['data'] = json_encode($post_data);
        $result['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $result['amount'] = $data['amount'];
        $result['merchNo'] = $data['merchNo'];

        unset($reqData);
        return $result;
    }

    /**
     * 二维码及语言包处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $responseData = json_decode($res,true);
        if ($responseData['msg'] == 'success'){
            $responseData['payUrl'] = json_decode($responseData['context'], true)['code_tt_url'];
        }
        return $responseData;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $res                = $request->getContent();
        $data = json_decode($res, true);
        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $post    = file_get_contents("php://input");
        $data = json_decode($post,true);
        $res = json_decode($data['context'], true);
        $signInfo = $data['sign'];
        unset($data['sign']);
        $signStr = md5(base64_encode($data['context']).$payConf['md5_private_key']);
        if (strtoupper($signStr) == strtoupper($signInfo) &&$res['orderState'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}