<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xingfapay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount

    /**
     * @param $reqData
     * @param $payConf
     * @return array
     */
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


        $data['mchid']      = (int)$payConf['business_num'];//商户ID
        $data['timestamp'] = time();
        $data['nonce'] = time() .rand(100000,999999);

        $post['paytype'] =(int)$bank ;
        $post['out_trade_no'] = $order;
        $post['goodsname'] = 'goodsname';
        $post['total_fee'] = sprintf('%.2f',$amount);
        $post['notify_url'] = $ServerUrl;
        $post['return_url'] = $returnUrl;
        $post['requestip'] = self::getIp();

        $post['mchid']      = (int)$data['mchid'];//商户ID
        $post['timestamp'] =  $data['timestamp'];
        $post['nonce'] = $data['nonce'];

        $signStr = self::getSignStr($post, true , true);
        $data['sign'] = md5("{$signStr}&key=".$payConf['md5_private_key']);
        unset($post['mchid']);
        unset($post['timestamp']);
        unset($post['nonce']);
        $data['data'] = $post;
        self::$header                   = [
            'Content-Type: application/json',
        ];
        $data = json_encode($data);
        unset($reqData);
        return $data;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['error'] == '0' && !empty($res['data']['payurl'])) {
            static::$result['appPath'] = $res['data']['payurl'];
            self::$pc = true;
        } else {
            static::$result['msg'] = $res['msg'];
            static::$result['code'] = $res['error'];
        }

    }


    public static function callback($request)
    {
        echo 'success'; // 接收callback 即同步返回 success msg

        $post     = file_get_contents("php://input");
        $data     = json_decode($post, true);
        if (!$payConf = self::getPayConf($data['out_trade_no'])) return false;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true , true);
        $isSign = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        if ($isSign == $sign ) {
            static::$amount = $data['total_fee']; // 下发金额设置 （注意单位）
            return true;
        } else {
            self::addCallbackMsg($request);
            return false;
        }
    }

}