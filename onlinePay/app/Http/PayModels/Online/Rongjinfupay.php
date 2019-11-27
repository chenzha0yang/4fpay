<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Rongjinfupay extends ApiModel
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

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        $payInfo = explode('@', $payConf['business_num']);
        if(!isset($payInfo[1])){
            echo '商户绑定错误,请参考:商户号@APPID';exit();
        }

        $data['app_id'] = $payInfo[1];//商户号
        $data['pay_type'] = $bank;//银行编码
        if((int)$payConf['pay_way'] === 1){
            $data['bank_code'] = $bank;
            $data['pay_type'] = 12;
        }
        $data['order_id'] = $order;//订单号
        $data['order_amt'] = sprintf('%.2f',$amount);//订单金额
        $data['time_stamp'] = date('YmdHis',time());
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        $data['goods_name'] = 'shuangyin';
        $data['user_ip'] = self::getIp();
        $signStr =  'app_id='.$data['app_id'].'&pay_type='.$data['pay_type'].'&order_id='.$data['order_id'].'&order_amt='.$data['order_amt'].'&notify_url='.$data['notify_url'].'&return_url='.$data['return_url'].'&time_stamp='.$data['time_stamp'];
        $data['sign'] = md5($signStr . "&key=" . md5($payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['status_code'] == '0') {
            $data['qrCode'] = $data['pay_data'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  'app_id='.$data['app_id'].'&order_id='.$data['order_id'].'&pay_seq='.$data['pay_seq'].'&pay_amt='.$data['pay_amt'].'&pay_result='.$data['pay_result'];
        $signTrue = md5($signStr . "&key=" . md5($payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['pay_result'] == '20') {
            return true;
        }
        return false;
    }


}