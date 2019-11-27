<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;


class Shuangjupay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // 回调金额



    public static function getData($reqData, $payConf)
    {

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$action = 'curlPost';


        $data['mchid']=(int)$payConf['business_num'];
        $data['timestamp']        = time();
        $data['nonce']            = md5(time());

        $arr['mchid']=(int)$payConf['business_num'];
        $arr['timestamp']        = time();
        $arr['nonce']            = md5(time());
        $arr['mchid']=(int)$payConf['business_num'];
        $arr['paytype']          = (int)$bank;
        $arr['out_trade_no']     = $order;
        $arr['goodsname']='ipad';
        $arr['total_fee']        = sprintf('%.2f',$amount);//订单金额,保留2位小数
        $arr['notify_url']       = $ServerUrl;
        $arr['return_url']       = $returnUrl;
        $arr['requestip']        = self::getIp();
        $data['data']=$arr;

        $signStr                  = self::getSignStr($arr, true, true);
        $data['sign']             = strtolower(md5($signStr . '&key=' . $payConf['md5_private_key']));

        $post = json_encode($data);
        self::$header = [
            "Content-Type: application/json; charset=utf-8"

        ];
        unset($reqData);
        return $post;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['error']== '0') {
            static::$result['appPath'] = $res['data']['payurl'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['msg'];
            static::$result['code'] = $res['error'];
        }

    }

    public static function callback($request)
    {

        echo 'success';

        $json = $request->getContent();
        $data=json_decode($json,true);

        $payConf = static::getPayConf($data['out_trade_no']);

        if (!$payConf) return false;

        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mysign  = md5($signStr . '&key=' . $payConf['md5_private_key']);

        if (strtolower($mysign) == strtolower($sign) ) {
            static::$amount = $data['total_fee'];  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }

}