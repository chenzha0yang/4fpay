<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yifubpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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
        self::$method = 'get';

        $data['cid'] = $payConf['business_num'];//商户号
        $data['uid'] = self::$UserName;
        $data['time'] = time();
        $data['amount'] = sprintf('%.2f',$amount);//订单金额
        $data['order_id'] = $order;//订单号
        $data['ip'] = self::getIp();
        $signStr =  self::getSignStr($data, true, false);
        $data['sign'] = base64_encode(hash_hmac('sha1', $signStr, $payConf['md5_private_key'], true));

        if((int)$payConf['pay_way'] === 1){
            $data['type'] = 'remit';
        }else{
            $data['type'] = 'qrcode';
        }

        if($data['type']){
            $data['tflag'] = $bank;
        }
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        if(isset($res['amount'])){
            $res['amount'] = $res['amount']/100;
        }
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {   
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign = $data['qsign'];
        unset($data['qsign']);
        $signStr =  'order_id='.$data['order_id'].'&amount='.$data['amount'].'&verified_time='.$data['verified_time'];
        $signTrue = base64_encode(hash_hmac('sha1', $signStr, $payConf['md5_private_key'], true));

        if (strtoupper($sign) == strtoupper($signTrue)  && $data['status'] == 'verified') {
            return true;
        }
        return false;
    }


}