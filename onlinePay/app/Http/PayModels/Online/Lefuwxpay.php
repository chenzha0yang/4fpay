<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Lefuwxpay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
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

        self::$unit    = 2;
        self::$resType = 'other';
        self::$payWay  = $payConf['pay_way'];
        self::$postType = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        $data = array(
            'cpId'          => $payConf['business_num'],
            'serviceId'     => $payConf['message1'], //10499
            'payType'       => $bank,
            'fee'           => $amount,
            'subject'       => 'subject',
            'description'   => 'description',
            'orderIdCp'     => $order,
            'notifyUrl'     => $ServerUrl,
            'callbackUrl'   => $returnUrl,
            'timestamp'     => self::getMillisecond(),
            'ip'            => request()->ip(),
            'version'       => '1',
        );
        $signStr      = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&",$payConf['md5_private_key']));
        //请求路径
        $url = $reqData['formUrl'];
        //组合新数组
        $post['url']       = $url;
        $post['data']      = $data;
        $post['orderIdCp'] = $data['orderIdCp'];
        $post['fee']       = $data['fee'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response){
        Curl::$method = 'header';
        Curl::$headerToArray = true;
        Curl::$header = [
            "Content-type:application/json;charset='utf-8'",
            "Accept:application/json"
        ];
        Curl::$request = json_encode($response['data']['data']);
        //请求网关
        Curl::$url = $response['data']['url'];
        //请求
        $result = Curl::Request();
        $responseData = json_decode($result['body'],true);
        $data = [];
        if($result['status'] == '200' && !empty($result['body'])){
            if($responseData['status'] == 0){
                $data['payUrl']   = $responseData['payUrl'];
//                $data['imageUrl'] = $responseData['imageUrl'];
            }else{
                $data['status']  = $responseData['status'];
                $data['message'] = $responseData['message'];
            }
        }

        return $data;
    }

    /**
     * @return bool|string
     */
    public static function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime, 0, 13);
    }
}