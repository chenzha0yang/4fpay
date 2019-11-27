<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Huanqiupay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //$returnUrl = $reqData['returnUrl'];// 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$unit = 2;
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        //TODO: do something

        $data = [
            'action'  =>$bank,
            'txnamt'  => $amount*100,
            'merid'   => $payConf['business_num'],
            'orderid' => $order,
            'backurl' => $ServerUrl,
        ];
        //格式化json字符串
        $jsonData   = json_encode($data);
        //输出Base64字符串
        $base64Data = base64_encode($jsonData);
        //拼接待签名字符
        $signData   = $base64Data . $payConf['md5_private_key'];
        //签名
        $sign = md5($signData);
        //拼接请求参数
        Curl::$request = "req=".urlencode($base64Data)."&sign=".$sign;
        //请求网关
        Curl::$url = $reqData['formUrl'];
        //请求
        $jsonData = Curl::Request();
        //解析数据
        $result = json_decode($jsonData, true);
        //解出返回明文
        $backJson = base64_decode($result['resp']);

        //数据组装
        $post = [];
        $post['json'] = $backJson;
        $post['orderid'] = $data['orderid'];
        $post['txnamt'] = $data['txnamt'];

        return $post;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $backData = json_decode($response['data']['json'], true);
        if ($backData['respcode'] == '00') {
            $postData['formaction'] = $backData['formaction'];
        } else {
            $postData['respcode'] = $backData['respcode'];
            $postData['respmsg']  = $backData['respmsg'];
        }

        return $postData;
    }

    /**
     * 回掉特殊处理
     * @param $model
     * @param $data - 返回的数据 - array
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf)
    {
        //获取数组中的数据 - 签名
        $callbackData = [
            'queryid' => $data['queryid'],
            'txnamt'  => $data['txnamt'],
            'merid'   => $data['merid'],
            'orderid' => $data['orderid'],
        ];
        //格式化json字符串
        $jsonData   = json_encode($callbackData);
        //输出Base64字符串
        $base64Data = base64_encode($jsonData);
        //拼接待签名字符
        $signData   = $base64Data . $payConf['md5_private_key'];
        //签名
        $sign = md5($signData);
        if($data['sign'] == $sign && $data['resultcode'] == '0000'){
            return true;
        }else{
            return false;
        }
    }

}