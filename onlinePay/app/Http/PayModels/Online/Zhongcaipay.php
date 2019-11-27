<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhongcaipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false;
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
        self::$changeUrl = true;
        $urls = file_get_contents($reqData['formUrl']);
        $urls = explode(',', $urls);

        if(!isset($urls[0])){
            echo '未获取到请求域名！请联系客服检查请求地址！';exit();
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['money'] = sprintf('%.2f',$amount);
        $data['outer_user_id'] = rand(1, 10000000);
        $data['merchant'] = $payConf['business_num'];
        $data['callback'] = $ServerUrl;
        $data['outer_order_sn'] = $order;
        $data['channel_code'] = 'JuhePay';
        $data['token'] = $payConf['md5_private_key'];

        $postData['queryUrl'] = $urls[0];
        $postData['data'] = $data;
        $postData['outer_order_sn'] = $data['outer_order_sn'];
        $postData['money'] = $data['money'];

        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['pay_url'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        if (strtoupper($payConf['md5_private_key']) == strtoupper($data['sign'])) {
            return true;
        }
        return false;
    }


}